<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\JournalPosting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * JournalService - Centralized Double-Entry Bookkeeping Logic
 *
 * Sesuai arsitektur dokumen: Semua transaksi finansial harus menghasilkan jurnal seimbang
 * Prinsip: Debit = Kredit (Double-Entry Bookkeeping)
 */
class JournalService
{
  /**
   * Create a balanced journal entry
   *
   * @param array $data [
   *   'posting_date' => Carbon,
   *   'description' => string,
   *   'referenceable' => Model (optional),
   *   'postings' => [
   *      ['account_id' => int, 'debit' => float, 'credit' => float, 'description' => string],
   *      ...
   *   ]
   * ]
   * @param bool $autoPost - Langsung post atau simpan sebagai draft
   * @return JournalEntry
   * @throws \Exception jika tidak balance
   */
  public function createJournalEntry(array $data, bool $autoPost = false): JournalEntry
  {
    // Validasi balance
    $this->validateBalance($data['postings']);

    return DB::transaction(function () use ($data, $autoPost) {
      // Generate transaction number
      $transactionNumber = $this->generateTransactionNumber();

      // Create header
      $journalEntry = JournalEntry::create([
        'transaction_number' => $transactionNumber,
        'posting_date' => $data['posting_date'] ?? Carbon::now(),
        'description' => $data['description'],
        'referenceable_type' => isset($data['referenceable']) ? get_class($data['referenceable']) : null,
        'referenceable_id' => $data['referenceable']->id ?? null,
        'is_posted' => $autoPost,
        'posted_at' => $autoPost ? Carbon::now() : null,
        'created_by' => auth()->id(),
        'posted_by' => $autoPost ? auth()->id() : null,
      ]);

      // Create detail postings
      foreach ($data['postings'] as $posting) {
        JournalPosting::create([
          'journal_entry_id' => $journalEntry->id,
          'account_id' => $posting['account_id'],
          'debit' => $posting['debit'] ?? 0,
          'credit' => $posting['credit'] ?? 0,
          'line_description' => $posting['description'] ?? null,
          'tags' => $posting['tags'] ?? null,
        ]);
      }

      return $journalEntry->fresh('postings');
    });
  }

  /**
   * Post a draft journal entry (lock it)
   */
  public function postJournalEntry(JournalEntry $entry): JournalEntry
  {
    if ($entry->is_posted) {
      throw new \Exception("Journal entry {$entry->transaction_number} sudah diposting sebelumnya!");
    }

    // Validasi ulang balance
    $this->validateBalance($entry->postings->toArray());

    $entry->update([
      'is_posted' => true,
      'posted_at' => Carbon::now(),
      'posted_by' => auth()->id(),
    ]);

    return $entry;
  }

  /**
   * Unpost a journal entry (untuk koreksi, hanya super_admin)
   */
  public function unpostJournalEntry(JournalEntry $entry): JournalEntry
  {
    if (!auth()->user()->hasRole('super_admin')) {
      throw new \Exception("Hanya Super Admin yang bisa unpost jurnal!");
    }

    $entry->update([
      'is_posted' => false,
      'posted_at' => null,
      'posted_by' => null,
    ]);

    return $entry;
  }

  /**
   * Validate that debits equal credits
   */
  protected function validateBalance(array $postings): void
  {
    $totalDebit = 0;
    $totalCredit = 0;

    foreach ($postings as $posting) {
      $totalDebit += $posting['debit'] ?? 0;
      $totalCredit += $posting['credit'] ?? 0;
    }

    // Toleransi floating point error (max 0.01 = 1 sen)
    if (abs($totalDebit - $totalCredit) > 0.01) {
      throw new \Exception(
        "Jurnal tidak balance! Debit: Rp " . number_format($totalDebit, 2) .
        ", Kredit: Rp " . number_format($totalCredit, 2)
      );
    }
  }

  /**
   * Generate unique transaction number: JE-YYYYMM-XXXX
   */
  protected function generateTransactionNumber(): string
  {
    $prefix = 'JE';
    $yearMonth = Carbon::now()->format('Ym');

    // Get latest number for this month
    $latest = JournalEntry::withTrashed()
      ->where('transaction_number', 'like', "{$prefix}-{$yearMonth}-%")
      ->orderBy('transaction_number', 'desc')
      ->first();

    if ($latest) {
      $lastNumber = (int) substr($latest->transaction_number, -4);
      $newNumber = $lastNumber + 1;
    } else {
      $newNumber = 1;
    }

    return sprintf('%s-%s-%04d', $prefix, $yearMonth, $newNumber);
  }

  /**
   * Get account balance (sum of all postings)
   *
   * @param int $accountId
   * @param Carbon|null $asOf - Balance as of date (default: today)
   * @return float
   */
  public function getAccountBalance(int $accountId, ?Carbon $asOf = null): float
  {
    $query = JournalPosting::query()
      ->whereHas('journalEntry', function ($q) use ($asOf) {
        $q->where('is_posted', true);
        if ($asOf) {
          $q->where('posting_date', '<=', $asOf);
        }
      })
      ->where('account_id', $accountId);

    $debits = (float) $query->sum('debit');
    $credits = (float) $query->sum('credit');

    // Balance tergantung tipe akun (Asset & Expense: Debit normal, Liability & Revenue: Credit normal)
    return $debits - $credits;
  }

  /**
   * Get trial balance (semua akun dengan saldo)
   *
   * @param Carbon|null $asOf
   * @return array
   */
  public function getTrialBalance(?Carbon $asOf = null): array
  {
    $postings = JournalPosting::query()
      ->select('account_id', DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
      ->whereHas('journalEntry', function ($q) use ($asOf) {
        $q->where('is_posted', true);
        if ($asOf) {
          $q->where('posting_date', '<=', $asOf);
        }
      })
      ->groupBy('account_id')
      ->with('account')
      ->get();

    $totalDebit = 0;
    $totalCredit = 0;
    $balances = [];

    foreach ($postings as $posting) {
      $debit = (float) $posting->total_debit;
      $credit = (float) $posting->total_credit;
      $balance = $debit - $credit;

      $totalDebit += $debit;
      $totalCredit += $credit;

      $balances[] = [
        'account' => $posting->account,
        'debit' => $debit,
        'credit' => $credit,
        'balance' => $balance,
      ];
    }

    return [
      'balances' => $balances,
      'total_debit' => $totalDebit,
      'total_credit' => $totalCredit,
      'is_balanced' => abs($totalDebit - $totalCredit) < 0.01,
    ];
  }
}
