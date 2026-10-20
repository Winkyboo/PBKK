using System.Windows;
using BookingKampus.ViewModels;

namespace BookingKampus.Views;

public partial class LoginWindow : Window
{
    public LoginWindow()
    {
        InitializeComponent();
        Loaded += (_, _) => UsernameBox.Focus();
    }

    async void OnLoginClick(object sender, RoutedEventArgs e)
    {
        if (DataContext is not LoginViewModel vm) return;
        vm.Password = PasswordBox.Password;
        await vm.LoginCommand.ExecuteAsync(null);
        PasswordBox.Clear();
    }
}
