using BookingKampus.Models;

namespace BookingKampus.Helpers;

public static class Session
{
    public static User Current { get; private set; }
    public static bool IsLoggedIn => Current != null;

    public static void SignIn(User user) => Current = user;
    public static void SignOut() => Current = null;

    public static bool InRole(params string[] roles) => Current != null && roles.Contains(Current.Role);

    /// <summary>Dipanggil di lapisan service dan repository, bukan hanya menyembunyikan tombol di UI.</summary>
    public static void Require(params string[] roles)
    {
        if (!InRole(roles))
            throw new UnauthorizedAccessException("Anda tidak memiliki akses untuk aksi ini.");
    }
}
