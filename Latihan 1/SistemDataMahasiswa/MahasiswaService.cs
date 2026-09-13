using System;
using System.Collections.Generic;
using System.Linq;

namespace DataMahasiswa
{
    public class MahasiswaService
    {
        private List<Mahasiswa> _daftarMahasiswa = new List<Mahasiswa>();

        public void Tambah(Mahasiswa m)
        {
            _daftarMahasiswa.Add(m);
        }

        public List<Mahasiswa> GetAll()
        {
            return _daftarMahasiswa;
        }

        public Mahasiswa CariByNim(string nim)
        {
            return _daftarMahasiswa.FirstOrDefault(m => 
                m.NIM.Equals(nim, StringComparison.OrdinalIgnoreCase));
        }

        public bool HapusByNim(string nim)
        {
            Mahasiswa m = CariByNim(nim);
            if (m != null)
            {
                _daftarMahasiswa.Remove(m);
                return true;
            }
            return false;
        }
    }
}