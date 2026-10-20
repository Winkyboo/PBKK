using BookingKampus.Helpers;
using BookingKampus.Models;
using BookingKampus.Repositories;

namespace BookingKampus.Services;

/// <summary>Aturan bisnis booking: validasi, deteksi bentrok, dan otorisasi.</summary>
public static class BookingService
{
    public static readonly TimeSpan Buka = TimeSpan.FromHours(7);
    public static readonly TimeSpan Tutup = TimeSpan.FromHours(21);

    public static async Task SubmitAsync(Ruangan ruangan, DateTime tanggal, TimeSpan mulai, TimeSpan selesai,
                                         string keperluan, IEnumerable<int> fasilitasIds)
    {
        Session.Require(Roles.All);
        if (ruangan == null) throw new AppException("Pilih ruangan terlebih dahulu.");
        if (!ruangan.IsActive) throw new AppException("Ruangan ini sedang tidak aktif.");
        if (tanggal.Date < DateTime.Today) throw new AppException("Tanggal booking tidak boleh di masa lalu.");
        if (selesai <= mulai) throw new AppException("Jam selesai harus setelah jam mulai.");
        if (mulai < Buka || selesai > Tutup) throw new AppException("Booking hanya dapat dilakukan pukul 07:00 sampai 21:00.");
        if (tanggal.Date == DateTime.Today && mulai < DateTime.Now.TimeOfDay) throw new AppException("Jam mulai sudah lewat.");

        keperluan = (keperluan ?? "").Trim();
        if (keperluan.Length < 5) throw new AppException("Keperluan wajib diisi, minimal 5 karakter.");
        if (keperluan.Length > 200) throw new AppException("Keperluan maksimal 200 karakter.");

        if (await BookingRepository.HasConflictAsync(ruangan.RuanganId, tanggal, mulai, selesai, 0))
            throw new AppException("Jadwal bentrok dengan booking lain yang sudah disetujui. Pilih jam atau ruangan lain.");

        await BookingRepository.InsertAsync(Session.Current.UserId, ruangan.RuanganId, tanggal, mulai, selesai,
                                            keperluan, fasilitasIds.ToList());
    }

    public static async Task CancelAsync(int bookingId)
    {
        Session.Require(Roles.All);
        var b = await BookingRepository.GetAsync(bookingId) ?? throw new AppException("Booking tidak ditemukan.");
        if (b.UserId != Session.Current.UserId && !Session.InRole(Roles.Admin))
            throw new UnauthorizedAccessException("Anda hanya dapat membatalkan booking milik sendiri.");
        if (!b.CanCancel) throw new AppException("Booking ini tidak dapat dibatalkan lagi.");

        await BookingRepository.SetStatusAsync(bookingId, BookingStatus.Dibatalkan, null);
    }

    public static async Task ApproveAsync(int bookingId, string catatan)
    {
        Session.Require(Roles.Admin);
        var b = await BookingRepository.GetAsync(bookingId) ?? throw new AppException("Booking tidak ditemukan.");
        if (b.Status != BookingStatus.Menunggu) throw new AppException("Booking ini sudah diproses.");
        if (await BookingRepository.HasConflictAsync(b.RuanganId, b.Tanggal, b.JamMulai, b.JamSelesai, b.BookingId))
            throw new AppException("Tidak dapat disetujui: jadwal bentrok dengan booking lain yang sudah disetujui.");

        await BookingRepository.SetStatusAsync(bookingId, BookingStatus.Disetujui, catatan);
    }

    public static async Task RejectAsync(int bookingId, string catatan)
    {
        Session.Require(Roles.Admin);
        if (string.IsNullOrWhiteSpace(catatan) || catatan.Trim().Length < 3)
            throw new AppException("Alasan penolakan wajib diisi.");
        var b = await BookingRepository.GetAsync(bookingId) ?? throw new AppException("Booking tidak ditemukan.");
        if (b.Status != BookingStatus.Menunggu) throw new AppException("Booking ini sudah diproses.");

        await BookingRepository.SetStatusAsync(bookingId, BookingStatus.Ditolak, catatan.Trim());
    }
}
