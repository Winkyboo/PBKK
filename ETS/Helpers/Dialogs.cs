using System.Windows;

namespace BookingKampus.Helpers;

public static class Dialogs
{
    public static bool Confirm(string message) =>
        MessageBox.Show(message, "Konfirmasi", MessageBoxButton.YesNo, MessageBoxImage.Question) == MessageBoxResult.Yes;
}
