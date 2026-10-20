using System.Collections.ObjectModel;
using BookingKampus.Helpers;
using BookingKampus.Models;
using BookingKampus.Repositories;
using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;

namespace BookingKampus.ViewModels;

public partial class RuanganViewModel : ViewModelBase
{
    public ObservableCollection<Ruangan> Items { get; } = new();
    public ObservableCollection<CheckItem> FasilitasChecks { get; } = new();

    [ObservableProperty] private string searchText = "";
    [ObservableProperty] private Ruangan selected;
    [ObservableProperty] private bool isEmpty;
    [ObservableProperty] private string kode = "";
    [ObservableProperty] private string nama = "";
    [ObservableProperty] private string gedung = "";
    [ObservableProperty] private string lantaiText = "1";
    [ObservableProperty] private string kapasitasText = "";
    [ObservableProperty] private string deskripsi = "";
    [ObservableProperty] private bool isActive = true;
    [ObservableProperty] private string editorTitle = "Ruangan baru";

    int _id;
    List<Fasilitas> _allFasilitas = new();

    public bool CanEdit => Session.InRole(Roles.Admin);
    public bool IsViewOnly => !CanEdit;

    public override async Task LoadAsync()
    {
        _allFasilitas = await FasilitasRepository.ListAsync("");
        await LoadItemsAsync();
        ResetForm();
    }

    async Task LoadItemsAsync()
    {
        Items.ReplaceWith(await RuanganRepository.ListAsync(SearchText));
        IsEmpty = Items.Count == 0;
    }

    partial void OnSearchTextChanged(string value) => _ = RunAsync(LoadItemsAsync);

    partial void OnSelectedChanged(Ruangan value)
    {
        if (value == null) return;
        _ = RunAsync(() => LoadIntoFormAsync(value));
    }

    async Task LoadIntoFormAsync(Ruangan r)
    {
        _id = r.RuanganId;
        Kode = r.Kode; Nama = r.Nama; Gedung = r.Gedung;
        LantaiText = r.Lantai.ToString(); KapasitasText = r.Kapasitas.ToString();
        Deskripsi = r.Deskripsi; IsActive = r.IsActive;
        EditorTitle = $"Ubah ruangan {r.Kode}";
        var ids = await RuanganRepository.FasilitasIdsAsync(r.RuanganId);
        FasilitasChecks.ReplaceWith(_allFasilitas.Select(f => new CheckItem
        {
            Id = f.FasilitasId,
            Nama = f.IsActive ? f.Nama : f.Nama + " (nonaktif)",
            IsChecked = ids.Contains(f.FasilitasId)
        }));
    }

    void ResetForm()
    {
        _id = 0;
        Kode = ""; Nama = ""; Gedung = ""; LantaiText = "1"; KapasitasText = ""; Deskripsi = ""; IsActive = true;
        EditorTitle = "Ruangan baru";
        FasilitasChecks.ReplaceWith(_allFasilitas.Select(f => new CheckItem
        {
            Id = f.FasilitasId,
            Nama = f.IsActive ? f.Nama : f.Nama + " (nonaktif)"
        }));
    }

    [RelayCommand]
    private void NewItem()
    {
        Selected = null;
        ResetForm();
    }

    Ruangan Validate()
    {
        var kodeV = (Kode ?? "").Trim();
        var namaV = (Nama ?? "").Trim();
        var gedungV = (Gedung ?? "").Trim();
        if (kodeV.Length == 0 || kodeV.Length > 20) throw new AppException("Kode ruangan wajib diisi, maksimal 20 karakter.");
        if (namaV.Length == 0 || namaV.Length > 100) throw new AppException("Nama ruangan wajib diisi, maksimal 100 karakter.");
        if (gedungV.Length == 0 || gedungV.Length > 50) throw new AppException("Gedung wajib diisi, maksimal 50 karakter.");
        if (!int.TryParse(LantaiText, out int lantai) || lantai < 0 || lantai > 50)
            throw new AppException("Lantai harus berupa angka 0 sampai 50.");
        if (!int.TryParse(KapasitasText, out int kap) || kap < 1 || kap > 5000)
            throw new AppException("Kapasitas harus berupa angka 1 sampai 5000.");
        if ((Deskripsi ?? "").Length > 200) throw new AppException("Deskripsi maksimal 200 karakter.");
        return new Ruangan
        {
            RuanganId = _id, Kode = kodeV, Nama = namaV, Gedung = gedungV, Lantai = lantai,
            Kapasitas = kap, Deskripsi = (Deskripsi ?? "").Trim(), IsActive = IsActive
        };
    }

    [RelayCommand]
    private async Task SaveAsync()
    {
        await RunAsync(async () =>
        {
            Session.Require(Roles.Admin);
            var room = Validate();
            await RuanganRepository.SaveAsync(room, FasilitasChecks.Where(c => c.IsChecked).Select(c => c.Id).ToList());
            await LoadItemsAsync();
            Selected = null;
            ResetForm();
        }, "Data ruangan disimpan.");
    }

    [RelayCommand]
    private async Task DeleteAsync()
    {
        if (_id == 0) { Toast.Show("Pilih ruangan yang akan dihapus dari tabel.", true); return; }
        if (!Dialogs.Confirm($"Hapus ruangan {Kode}? Tindakan ini tidak dapat dibatalkan.")) return;
        await RunAsync(async () =>
        {
            Session.Require(Roles.Admin);
            await RuanganRepository.DeleteAsync(_id);
            await LoadItemsAsync();
            Selected = null;
            ResetForm();
        }, "Ruangan dihapus.");
    }
}
