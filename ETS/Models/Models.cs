using System.Globalization;
using CommunityToolkit.Mvvm.ComponentModel;

namespace BookingKampus.Models;

public static class Roles
{
    public const string Admin = "Admin";
    public const string Pemohon = "Pemohon";
    public static readonly string[] All = { Admin, Pemohon };

    public static string Display(string role) => role == Admin ? "Administrator" : "Pemohon";
}

public static class BookingStatus
{
    public const string Menunggu = "Menunggu";
    public const string Disetujui = "Disetujui";
    public const string Ditolak = "Ditolak";
    public const string Dibatalkan = "Dibatalkan";
    public static readonly string[] All = { Menunggu, Disetujui, Ditolak, Dibatalkan };
}

public class User
{
    public int UserId { get; set; }
    public string Username { get; set; }
    public string PasswordHash { get; set; }
    public string Nama { get; set; }
    public string Email { get; set; }
    public string Role { get; set; }
    public bool IsActive { get; set; }
    public string RoleText => Roles.Display(Role);
    public string StatusText => IsActive ? "Aktif" : "Nonaktif";
}

public class Fasilitas
{
    public int FasilitasId { get; set; }
    public string Nama { get; set; }
    public string Deskripsi { get; set; }
    public bool IsActive { get; set; }
    public string StatusText => IsActive ? "Aktif" : "Nonaktif";
}

public class Ruangan
{
    public int RuanganId { get; set; }
    public string Kode { get; set; }
    public string Nama { get; set; }
    public string Gedung { get; set; }
    public int Lantai { get; set; }
    public int Kapasitas { get; set; }
    public string Deskripsi { get; set; }
    public bool IsActive { get; set; }
    public string FasilitasText { get; set; }
    public string Lokasi => $"{Gedung}, lantai {Lantai}";
    public string StatusText => IsActive ? "Aktif" : "Nonaktif";
    public string DisplayName => $"{Nama} ({Kode}), kapasitas {Kapasitas}";
}

public class Booking
{
    static readonly CultureInfo Id = CultureInfo.GetCultureInfo("id-ID");
    public int BookingId { get; set; }
    public int UserId { get; set; }
    public string PemohonNama { get; set; }
    public int RuanganId { get; set; }
    public string RuanganNama { get; set; }
    public DateTime Tanggal { get; set; }
    public TimeSpan JamMulai { get; set; }
    public TimeSpan JamSelesai { get; set; }
    public string Keperluan { get; set; }
    public string Status { get; set; }
    public string CatatanApprover { get; set; }
    public DateTime CreatedAt { get; set; }
    public string TanggalText => Tanggal.ToString("dd MMM yyyy", Id);
    public string Waktu => $"{JamMulai:hh\\:mm} - {JamSelesai:hh\\:mm}";
    public bool CanCancel => Status == BookingStatus.Menunggu || Status == BookingStatus.Disetujui;
}

public partial class CheckItem : ObservableObject
{
    public int Id { get; set; }
    public string Nama { get; set; }
    [ObservableProperty] private bool isChecked;
}

public class DashboardStats
{
    public int Menunggu { get; set; }
    public int RuanganAktif { get; set; }
    public int FasilitasAktif { get; set; }
    public int DisetujuiBulanIni { get; set; }
}
