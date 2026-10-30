using System.Collections.ObjectModel;
using System.ComponentModel;
using System.Linq;
using System.Runtime.CompilerServices;
using System.Windows.Input;
using StudentManager.Data;
using StudentManager.Models;

namespace StudentManager.ViewModels;

public class StudentViewModel : INotifyPropertyChanged
{
    private readonly StudentRepository _repository;

    public ObservableCollection<Student> Students { get; } = new();

    private Student? _selectedStudent;
    public Student? SelectedStudent
    {
        get => _selectedStudent;
        set
        {
            _selectedStudent = value;
            OnPropertyChanged();
        }
    }

    private string _searchText = string.Empty;
    public string SearchText
    {
        get => _searchText;
        set
        {
            _searchText = value;
            OnPropertyChanged();
        }
    }

    public int TotalStudents => Students.Count;
    public int TotalInformatika => Students.Count(x => x.Prodi == "Teknik Informatika");
    public int TotalIndustri => Students.Count(x => x.Prodi == "Teknik Industri");
    public int TotalAktuaria => Students.Count(x => x.Prodi == "Aktuaria");
    public int TotalLakiLaki => Students.Count(x => x.Gender == "Laki-laki");
    public int TotalPerempuan => Students.Count(x => x.Gender == "Perempuan");

    public ICommand SaveCommand { get; }
    public ICommand DeleteCommand { get; }
    public ICommand ResetCommand { get; }
    public ICommand SearchCommand { get; }

    public StudentViewModel()
    {
        _repository = new StudentRepository();

        SaveCommand = new RelayCommand(Save);
        DeleteCommand = new RelayCommand(Delete);
        ResetCommand = new RelayCommand(Reset);
        SearchCommand = new RelayCommand(Search);

        LoadData();
        Reset();
    }

    private void LoadData()
    {
        try
        {
            Students.Clear();

            foreach (var student in _repository.GetAll())
            {
                Students.Add(student);
            }

            RefreshStatistics();
        }
        catch (System.Exception ex)
        {
            System.Windows.MessageBox.Show(
                $"Database Connection Error:\n{ex.Message}", 
                "Startup Error", 
                System.Windows.MessageBoxButton.OK, 
                System.Windows.MessageBoxImage.Error);
        }
    }

    private void Save()
    {
        if (SelectedStudent == null) return;
    
        // 1. Validasi Input Kosong
        if (string.IsNullOrWhiteSpace(SelectedStudent.NRP) ||
            string.IsNullOrWhiteSpace(SelectedStudent.Nama) ||
            string.IsNullOrWhiteSpace(SelectedStudent.Prodi) ||
            string.IsNullOrWhiteSpace(SelectedStudent.Gender))
        {
            System.Windows.MessageBox.Show(
                "Kolom NRP, Nama, Prodi, dan Gender wajib diisi!", 
                "Validasi Gagal", 
                System.Windows.MessageBoxButton.OK, 
                System.Windows.MessageBoxImage.Warning);
            return;
        }
    
        string nrp = SelectedStudent.NRP.Trim();
    
        // MODE 1: TAMBAH DATA BARU (Id == 0)
        if (SelectedStudent.Id == 0)
        {
            // Cek apakah NRP sudah terdaftar di database
            if (_repository.IsNrpExists(nrp))
            {
                System.Windows.MessageBox.Show(
                    $"Data mahasiswa dengan NRP '{nrp}' sudah ada dalam database!", 
                    "Data Sudah Ada", 
                    System.Windows.MessageBoxButton.OK, 
                    System.Windows.MessageBoxImage.Warning);
                return;
            }
    
            try
            {
                _repository.Insert(SelectedStudent);
                LoadData();
                Reset();
    
                System.Windows.MessageBox.Show(
                    "Data mahasiswa berhasil disimpan!", 
                    "Sukses", 
                    System.Windows.MessageBoxButton.OK, 
                    System.Windows.MessageBoxImage.Information);
            }
            catch (System.Exception ex)
            {
                System.Windows.MessageBox.Show(
                    $"Gagal menyimpan data ke database:\n{ex.Message}", 
                    "Error", 
                    System.Windows.MessageBoxButton.OK, 
                    System.Windows.MessageBoxImage.Error);
            }
        }
        // MODE 2: EDIT DATA TERSELEKSI (Id > 0)
        else
        {
            // Cek apakah NRP diubah menjadi milik mahasiswa lain
            if (_repository.IsNrpExistsForOtherStudent(nrp, SelectedStudent.Id))
            {
                System.Windows.MessageBox.Show(
                    $"NRP '{nrp}' sudah digunakan oleh mahasiswa lain!", 
                    "NRP Duplikat", 
                    System.Windows.MessageBoxButton.OK, 
                    System.Windows.MessageBoxImage.Warning);
                return;
            }
    
            // Cek apakah data yang diketik sama persis dengan yang ada di database (tidak ada perubahan)
            var existingInDb = _repository.GetById(SelectedStudent.Id);
            if (existingInDb != null &&
                existingInDb.NRP == SelectedStudent.NRP.Trim() &&
                existingInDb.Nama == SelectedStudent.Nama.Trim() &&
                existingInDb.Prodi == SelectedStudent.Prodi &&
                existingInDb.Gender == SelectedStudent.Gender &&
                (existingInDb.Email ?? "") == (SelectedStudent.Email ?? "").Trim())
            {
                System.Windows.MessageBox.Show(
                    "Data mahasiswa sudah ada dan tidak ada perubahan data yang dilakukan.", 
                    "Tidak Ada Perubahan", 
                    System.Windows.MessageBoxButton.OK, 
                    System.Windows.MessageBoxImage.Information);
                return;
            }
    
            try
            {
                _repository.Update(SelectedStudent);
                LoadData();
                Reset();
    
                System.Windows.MessageBox.Show(
                    "Data mahasiswa berhasil diperbarui!", 
                    "Sukses", 
                    System.Windows.MessageBoxButton.OK, 
                    System.Windows.MessageBoxImage.Information);
            }
            catch (System.Exception ex)
            {
                System.Windows.MessageBox.Show(
                    $"Gagal memperbarui data di database:\n{ex.Message}", 
                    "Error", 
                    System.Windows.MessageBoxButton.OK, 
                    System.Windows.MessageBoxImage.Error);
            }
        }
    }

    private void Delete()
    {
        if (SelectedStudent == null || SelectedStudent.Id == 0)
        {
            System.Windows.MessageBox.Show(
                "Silakan klik/pilih salah satu baris mahasiswa pada tabel terlebih dahulu untuk menghapus.",
                "Peringatan",
                System.Windows.MessageBoxButton.OK,
                System.Windows.MessageBoxImage.Warning);
            return;
        }
    
        var result = System.Windows.MessageBox.Show(
            $"Apakah Anda yakin ingin menghapus data mahasiswa:\nNama: {SelectedStudent.Nama}\nNRP: {SelectedStudent.NRP}?",
            "Konfirmasi Hapus",
            System.Windows.MessageBoxButton.YesNo,
            System.Windows.MessageBoxImage.Question);

        if (result == System.Windows.MessageBoxResult.Yes)
        {
            try
            {
                _repository.Delete(SelectedStudent.Id);
                LoadData();
                Reset();

                System.Windows.MessageBox.Show(
                    "Data mahasiswa berhasil dihapus!", 
                    "Sukses", 
                    System.Windows.MessageBoxButton.OK, 
                    System.Windows.MessageBoxImage.Information);
            }
            catch (System.Exception ex)
            {
                System.Windows.MessageBox.Show(
                    $"Gagal menghapus data dari database:\n{ex.Message}", 
                    "Error Hapus", 
                    System.Windows.MessageBoxButton.OK, 
                    System.Windows.MessageBoxImage.Error);
            }
        }
    }

        private void Search()
        {
        var result = string.IsNullOrWhiteSpace(SearchText)
            ? _repository.GetAll()
            : _repository.Search(SearchText);

        Students.Clear();

        foreach (var student in result)
        {
            Students.Add(student);
        }

        RefreshStatistics();
    }

    private void Reset()
    {
        SelectedStudent = new Student();
    }

    private void RefreshStatistics()
    {
        OnPropertyChanged(nameof(TotalStudents));
        OnPropertyChanged(nameof(TotalInformatika));
        OnPropertyChanged(nameof(TotalIndustri));
        OnPropertyChanged(nameof(TotalAktuaria));
        OnPropertyChanged(nameof(TotalLakiLaki));
        OnPropertyChanged(nameof(TotalPerempuan));
    }

    public event PropertyChangedEventHandler? PropertyChanged;

    protected void OnPropertyChanged([CallerMemberName] string? propertyName = null)
    {
        PropertyChanged?.Invoke(this, new PropertyChangedEventArgs(propertyName));
    }
}