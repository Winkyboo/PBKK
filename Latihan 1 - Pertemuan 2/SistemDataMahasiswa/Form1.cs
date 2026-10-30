using System;
using System.Drawing;
using System.Windows.Forms;

namespace DataMahasiswa
{
    public class Form1 : Form
    {
        private MahasiswaService _service = new MahasiswaService();
        private TextBox txtNIM, txtNama, txtProdi, txtIPK, txtCariNIM;
        private Button btnTambah, btnCari, btnHapus, btnClear;
        private DataGridView dgvMahasiswa;
        private Panel sidebarPanel, tablePanel;

        public Form1()
        {
            InitializeComponent();
        }

        private void InitializeComponent()
        {
            Color bgCanvas = Color.FromArgb(18, 18, 24);         
            Color cardBg = Color.FromArgb(28, 28, 38);           
            Color inputBg = Color.FromArgb(38, 38, 52);          
            Color textPrimary = Color.FromArgb(240, 240, 245);   
            Color textMuted = Color.FromArgb(140, 140, 160);     
            Color accentPurple = Color.FromArgb(114, 9, 183);   
            Color accentRed = Color.FromArgb(229, 56, 59);       
            Color borderGrid = Color.FromArgb(45, 45, 60);

            this.Text = "Dashboard Data Mahasiswa";
            this.Size = new Size(950, 600);
            this.StartPosition = FormStartPosition.CenterScreen;
            this.BackColor = bgCanvas;
            this.ForeColor = textPrimary;
            this.MinimumSize = new Size(900, 550);

            // Input Form Panel
            sidebarPanel = new Panel()
            {
                Location = new Point(20, 20),
                Size = new Size(300, 520),
                BackColor = cardBg,
                Padding = new Padding(15)
            };

            Label lblSidebarTitle = new Label()
            {
                Text = "Form Input Data",
                Font = new Font("Segoe UI", 14, FontStyle.Bold),
                ForeColor = textPrimary,
                Location = new Point(15, 15),
                AutoSize = true
            };

            // Form Inputs (Vertical Stack)
            Label lblNIM = CreateLabel("NIM", new Point(15, 60), textMuted);
            txtNIM = CreateStyledTextBox(new Point(15, 80), 270, inputBg, textPrimary);

            Label lblNama = CreateLabel("Nama Lengkap", new Point(15, 130), textMuted);
            txtNama = CreateStyledTextBox(new Point(15, 150), 270, inputBg, textPrimary);

            Label lblProdi = CreateLabel("Program Studi", new Point(15, 200), textMuted);
            txtProdi = CreateStyledTextBox(new Point(15, 220), 270, inputBg, textPrimary);

            Label lblIPK = CreateLabel("IPK (0.00 - 4.00)", new Point(15, 270), textMuted);
            txtIPK = CreateStyledTextBox(new Point(15, 290), 270, inputBg, textPrimary);

            // Sidebar Action Buttons
            btnTambah = CreateStyledButton("Simpan Data", new Point(15, 360), 270, 38, accentPurple, Color.White);
            btnTambah.Click += BtnTambah_Click;

            btnClear = CreateStyledButton("Reset Input", new Point(15, 410), 270, 32, Color.FromArgb(55, 55, 75), textPrimary);
            btnClear.Click += (s, e) => ClearForm();

            sidebarPanel.Controls.AddRange(new Control[] {
                lblSidebarTitle, lblNIM, txtNIM, lblNama, txtNama, 
                lblProdi, txtProdi, lblIPK, txtIPK, btnTambah, btnClear 
            });

            // Search Bar & Data Grid
            tablePanel = new Panel()
            {
                Location = new Point(340, 20),
                Size = new Size(570, 520),
                BackColor = cardBg
            };

            Label lblTableTitle = new Label()
            {
                Text = "Daftar Mahasiswa",
                Font = new Font("Segoe UI", 14, FontStyle.Bold),
                ForeColor = textPrimary,
                Location = new Point(15, 15),
                AutoSize = true
            };

            // Top Search & Quick Action Bar inside Right Panel
            txtCariNIM = CreateStyledTextBox(new Point(15, 55), 230, inputBg, textPrimary);
            
            btnCari = CreateStyledButton("Cari NIM", new Point(255, 54), 90, 32, Color.FromArgb(72, 149, 239), Color.White);
            btnCari.Click += BtnCari_Click;

            btnHapus = CreateStyledButton("Hapus", new Point(355, 54), 90, 32, accentRed, Color.White);
            btnHapus.Click += BtnHapus_Click;

            // Table Grid View Setup
            dgvMahasiswa = new DataGridView()
            {
                Location = new Point(15, 100),
                Size = new Size(540, 400),
                AutoGenerateColumns = true,
                AutoSizeColumnsMode = DataGridViewAutoSizeColumnsMode.Fill,
                ReadOnly = true,
                BackgroundColor = cardBg,
                BorderStyle = BorderStyle.None,
                EnableHeadersVisualStyles = false,
                GridColor = borderGrid,
                SelectionMode = DataGridViewSelectionMode.FullRowSelect,
                RowHeadersVisible = false,
                AllowUserToAddRows = false
            };

            // Modern Header & Cell Colors
            dgvMahasiswa.ColumnHeadersDefaultCellStyle.BackColor = Color.FromArgb(40, 40, 55);
            dgvMahasiswa.ColumnHeadersDefaultCellStyle.ForeColor = textPrimary;
            dgvMahasiswa.ColumnHeadersDefaultCellStyle.Font = new Font("Segoe UI", 9.5f, FontStyle.Bold);
            dgvMahasiswa.ColumnHeadersHeight = 36;

            dgvMahasiswa.DefaultCellStyle.BackColor = cardBg;
            dgvMahasiswa.DefaultCellStyle.ForeColor = textPrimary;
            dgvMahasiswa.DefaultCellStyle.SelectionBackColor = accentPurple;
            dgvMahasiswa.DefaultCellStyle.SelectionForeColor = Color.White;
            dgvMahasiswa.DefaultCellStyle.Font = new Font("Segoe UI", 9);

            // Populate form when row is clicked
            dgvMahasiswa.CellClick += DgvMahasiswa_CellClick;

            tablePanel.Controls.AddRange(new Control[] {
                lblTableTitle, txtCariNIM, btnCari, btnHapus, dgvMahasiswa 
            });

            // Register Layout Panels
            this.Controls.Add(sidebarPanel);
            this.Controls.Add(tablePanel);
        }

        // Helper Methods 
        private Label CreateLabel(string text, Point loc, Color color)
        {
            return new Label() { Text = text, Location = loc, AutoSize = true, ForeColor = color, Font = new Font("Segoe UI", 8.5f, FontStyle.Bold) };
        }

        private TextBox CreateStyledTextBox(Point location, int width, Color bgColor, Color fgColor)
        {
            return new TextBox()
            {
                Location = location,
                Width = width,
                BackColor = bgColor,
                ForeColor = fgColor,
                BorderStyle = BorderStyle.FixedSingle,
                Font = new Font("Segoe UI", 10)
            };
        }

        private Button CreateStyledButton(string text, Point location, int width, int height, Color bgColor, Color fgColor)
        {
            Button btn = new Button()
            {
                Text = text,
                Location = location,
                Size = new Size(width, height),
                BackColor = bgColor,
                ForeColor = fgColor,
                FlatStyle = FlatStyle.Flat,
                Font = new Font("Segoe UI", 9, FontStyle.Bold),
                Cursor = Cursors.Hand
            };
            btn.FlatAppearance.BorderSize = 0;
            return btn;
        }

        private void RefreshGrid()
        {
            dgvMahasiswa.DataSource = null;
            dgvMahasiswa.DataSource = _service.GetAll();
        }

        private void ClearForm()
        {
            txtNIM.Clear();
            txtNama.Clear();
            txtProdi.Clear();
            txtIPK.Clear();
            txtCariNIM.Clear();
        }

        // Event Handlers
        private void BtnTambah_Click(object sender, EventArgs e)
        {
            if (string.IsNullOrWhiteSpace(txtNIM.Text) || string.IsNullOrWhiteSpace(txtNama.Text))
            {
                MessageBox.Show("NIM dan Nama wajib diisi!", "Peringatan", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            if (!double.TryParse(txtIPK.Text, out double ipk) || ipk < 0 || ipk > 4)
            {
                MessageBox.Show("IPK harus angka valid antara 0.00 - 4.00", "Gagal", MessageBoxButtons.OK, MessageBoxIcon.Error);
                return;
            }

            _service.Tambah(new Mahasiswa(txtNIM.Text, txtNama.Text, txtProdi.Text, ipk));
            RefreshGrid();
            ClearForm();
            MessageBox.Show("Mahasiswa berhasil tersimpan!", "Info", MessageBoxButtons.OK, MessageBoxIcon.Information);
        }

        private void BtnCari_Click(object sender, EventArgs e)
        {
            Mahasiswa m = _service.CariByNim(txtCariNIM.Text);
            if (m != null)
            {
                txtNIM.Text = m.NIM;
                txtNama.Text = m.Nama;
                txtProdi.Text = m.Prodi;
                txtIPK.Text = m.IPK.ToString("F2");
                MessageBox.Show("Data ditemukan & dimuat ke Sidebar!", "Hasil", MessageBoxButtons.OK, MessageBoxIcon.Information);
            }
            else
            {
                MessageBox.Show("Mahasiswa tidak ditemukan.", "Hasil", MessageBoxButtons.OK, MessageBoxIcon.Warning);
            }
        }

        private void BtnHapus_Click(object sender, EventArgs e)
        {
            string targetNim = string.IsNullOrWhiteSpace(txtCariNIM.Text) ? txtNIM.Text : txtCariNIM.Text;

            if (_service.HapusByNim(targetNim))
            {
                RefreshGrid();
                ClearForm();
                MessageBox.Show("Data berhasil dihapus!", "Info", MessageBoxButtons.OK, MessageBoxIcon.Information);
            }
            else
            {
                MessageBox.Show("NIM tidak ditemukan untuk dihapus.", "Gagal", MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
        }

        private void DgvMahasiswa_CellClick(object sender, DataGridViewCellEventArgs e)
        {
            if (e.RowIndex >= 0 && dgvMahasiswa.Rows[e.RowIndex].DataBoundItem is Mahasiswa selected)
            {
                txtNIM.Text = selected.NIM;
                txtNama.Text = selected.Nama;
                txtProdi.Text = selected.Prodi;
                txtIPK.Text = selected.IPK.ToString("F2");
                txtCariNIM.Text = selected.NIM;
            }
        }
    }
}