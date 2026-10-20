using System.Windows;
using BookingKampus.Helpers;
using BookingKampus.Services;
using BookingKampus.ViewModels;
using BookingKampus.Views;

namespace BookingKampus;

public partial class App : Application
{
    protected override async void OnStartup(StartupEventArgs e)
    {
        base.OnStartup(e);

        DispatcherUnhandledException += (_, args) =>
        {
            Logger.Write(args.Exception);
            MessageBox.Show("Terjadi kesalahan yang tidak terduga. Detail teknis sudah dicatat di file log.",
                "Booking Kampus", MessageBoxButton.OK, MessageBoxImage.Error);
            args.Handled = true;
        };

        try
        {
            Config.Load();
            await DbInitializer.EnsureAsync();
        }
        catch (Exception ex)
        {
            Logger.Write(ex);
            MessageBox.Show(
                "Database tidak dapat disiapkan. Pastikan SQL Server berjalan dan connection string di appsettings.json sudah benar.\n\nRincian: " + ex.Message,
                "Booking Kampus", MessageBoxButton.OK, MessageBoxImage.Error);
            Shutdown();
            return;
        }

        ShowLogin();
    }

    void ShowLogin()
    {
        var vm = new LoginViewModel();
        var window = new LoginWindow { DataContext = vm };
        vm.LoggedIn += () =>
        {
            ShowMain();
            window.Close();
        };
        MainWindow = window;
        window.Show();
    }

    void ShowMain()
    {
        var vm = new MainViewModel();
        var window = new Views.MainWindow { DataContext = vm };
        vm.LoggedOut += () =>
        {
            Session.SignOut();
            ShowLogin();
            window.Close();
        };
        window.Closed += (_, _) => vm.Dispose();
        MainWindow = window;
        window.Show();
    }
}
