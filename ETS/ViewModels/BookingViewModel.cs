using System.Collections.ObjectModel;
using BookingKampus.Helpers;
using BookingKampus.Models;
using BookingKampus.Repositories;
using BookingKampus.Services;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;

namespace BookingKampus.ViewModels;

public partial class BookingViewModel : ViewModelBase
{
    const string HintChoose = "Pilih ruangan dan tanggal untuk melihat jadwal.";

    public ObservableCollection<Ruangan> Ruangans { get; } = new();
    public ObservableCollection<CheckItem> FasilitasChecks { get; } = new();
    public ObservableCollection<Booking> Schedule { get; } = new();
    public ObservableCollection<Booking> MyBookings { get; } = new();

    public List<string> TimeSlots { get; } = Enumerable.Range(14, 29)
        .Select(i => TimeSpan.FromMinutes(i * 30).ToString(@"hh\:mm")).ToList();

    public DateTime MinDate => DateTime.Today;

    [ObservableProperty] private Ruangan selectedRuangan;
    [ObservableProperty] private DateTime? tanggal = DateTime.Today;
    [ObservableProperty] private string selectedMulai = "08:00";
    [ObservableProperty] private string selectedSelesai = "10:00";
    [ObservableProperty] private string keperluan = "";
    [ObservableProperty] private string scheduleHint = HintChoose;
    [ObservableProperty] private bool isMyEmpty;

    public override async Task LoadAsync()
    {
        Ruangans.ReplaceWith(await RuanganRepository.ListAsync("", true));
        await LoadMineAsync();
    }

    async Task LoadMineAsync()
    {
        MyBookings.ReplaceWith(await BookingRepository.ListByUserAsync(Session.Current.UserId));
        IsMyEmpty = MyBookings.Count == 0;
    }

    partial void OnSelectedRuanganChanged(Ruangan value) => _ = RunAsync(RefreshContextAsync);
    partial void OnTanggalChanged(DateTime? value) => _ = RunAsync(RefreshScheduleAsync);

    async Task RefreshContextAsync()
    {
        FasilitasChecks.Clear();
        if (SelectedRuangan != null)
        {
            var list = await RuanganRepository.FasilitasOfRuanganAsync(SelectedRuangan.RuanganId);
            FasilitasChecks.ReplaceWith(list.Select(f => new CheckItem { Id = f.FasilitasId, Nama = f.Nama }));
        }
        await RefreshScheduleAsync();
    }

    async Task RefreshScheduleAsync()
    {
        if (SelectedRuangan == null || Tanggal == null)
        {
            Schedule.Clear();
            ScheduleHint = HintChoose;
            return;
        }
        var list = await BookingRepository.ListScheduleAsync(SelectedRuangan.RuanganId, Tanggal.Value);
        Schedule.ReplaceWith(list);
        ScheduleHint = list.Count == 0 ? "Belum ada booking pada tanggal ini. Ruangan kosong sepanjang hari." : "";
    }

    [RelayCommand]
    private async Task SubmitAsync()
    {
        await RunAsync(async () =>
        {
            if (SelectedRuangan == null) throw new AppException("Pilih ruangan terlebih dahulu.");
            if (Tanggal == null) throw new AppException("Pilih tanggal booking.");
            if (SelectedMulai == null || SelectedSelesai == null) throw new AppException("Pilih jam mulai dan jam selesai.");

            await BookingService.SubmitAsync(SelectedRuangan, Tanggal.Value,
                TimeSpan.Parse(SelectedMulai), TimeSpan.Parse(SelectedSelesai), Keperluan,
                FasilitasChecks.Where(c => c.IsChecked).Select(c => c.Id));

            Keperluan = "";
            foreach (var c in FasilitasChecks) c.IsChecked = false;
            await LoadMineAsync();
            await RefreshScheduleAsync();
        }, "Pengajuan terkirim. Menunggu persetujuan petugas.");
    }

    [RelayCommand]
    private async Task CancelAsync(Booking booking)
    {
        if (booking == null) return;
        if (!Dialogs.Confirm($"Batalkan booking {booking.RuanganNama} pada {booking.TanggalText}, {booking.Waktu}?")) return;
        await RunAsync(async () =>
        {
            await BookingService.CancelAsync(booking.BookingId);
            await LoadMineAsync();
            await RefreshScheduleAsync();
        }, "Booking dibatalkan.");
    }
}
