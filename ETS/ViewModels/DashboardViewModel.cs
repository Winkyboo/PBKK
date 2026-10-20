using System.Collections.ObjectModel;
using BookingKampus.Helpers;
using BookingKampus.Models;
using BookingKampus.Repositories;
using CommunityToolkit.Mvvm.ComponentModel;

namespace BookingKampus.ViewModels;

public partial class DashboardViewModel : ViewModelBase
{
    public ObservableCollection<Booking> Today { get; } = new();

    [ObservableProperty] private DashboardStats stats = new();
    [ObservableProperty] private bool todayEmpty;

    public string Sapaan => $"Halo, {Session.Current.Nama}. Berikut kondisi pemesanan saat ini.";

    public override async Task LoadAsync()
    {
        Stats = await StatistikRepository.GetStatsAsync();
        Today.ReplaceWith(await BookingRepository.ListTodayApprovedAsync());
        TodayEmpty = Today.Count == 0;
    }
}
