namespace BookingKampus.Helpers;

/// <summary>Kesalahan yang pesannya aman dan boleh ditampilkan langsung ke pengguna.</summary>
public class AppException : Exception
{
    public AppException(string message) : base(message) { }
}
