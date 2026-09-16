using System;
using System.Data;
using System.Drawing;
using System.Windows.Forms;

namespace CalculatorApp
{
    public class Form1 : Form
    {
        private bool isCalculated = false;
        private Label lblHistory;
        private TextBox txtDisplay;
        private Button btn0, btn1, btn2, btn3, btn4, btn5, btn6, btn7, btn8, btn9;
        private Button btnPlus, btnMinus, btnMultiply, btnDivide;
        private Button btnDecimal, btnClear, btnEquals, btnBackspace, btnSign, btnPercent;

        public Form1()
        {
            InitializeComponent();
        }

        private void InitializeComponent()
        {
            Color bgCanvas = Color.FromArgb(15, 23, 42);       
            Color displayBg = Color.FromArgb(30, 41, 59);      
            Color btnNumBg = Color.FromArgb(30, 41, 59);       
            Color btnFuncBg = Color.FromArgb(51, 65, 85);      
            Color btnFuncFg = Color.FromArgb(56, 189, 248);    
            Color btnOpBg = Color.FromArgb(139, 92, 246);      
            Color btnEqualsBg = Color.FromArgb(124, 58, 237);  
            Color textLight = Color.FromArgb(248, 250, 252);   
            Color textMuted = Color.FromArgb(148, 163, 184);   

            this.Text = "Calculator App";
            this.Size = new Size(360, 480);
            this.StartPosition = FormStartPosition.CenterScreen;
            this.FormBorderStyle = FormBorderStyle.FixedSingle;
            this.MaximizeBox = false;
            this.BackColor = bgCanvas;
            this.ForeColor = textLight;

            lblHistory = new Label()
            {
                Text = "",
                Font = new Font("Segoe UI", 10, FontStyle.Regular),
                Location = new Point(20, 15),
                Size = new Size(304, 22),
                TextAlign = ContentAlignment.MiddleRight,
                ForeColor = textMuted
            };

            //Main Input & Output Box
            txtDisplay = new TextBox()
            {
                Text = "0",
                Font = new Font("Segoe UI", 20, FontStyle.Bold),
                Location = new Point(20, 40),
                Width = 304,
                Height = 45,
                ReadOnly = true,
                TextAlign = HorizontalAlignment.Right,
                BackColor = displayBg,
                ForeColor = textLight,
                BorderStyle = BorderStyle.FixedSingle
            };

            btn0 = CreateButton("0", btnNumBg, textLight);
            btn1 = CreateButton("1", btnNumBg, textLight);
            btn2 = CreateButton("2", btnNumBg, textLight);
            btn3 = CreateButton("3", btnNumBg, textLight);
            btn4 = CreateButton("4", btnNumBg, textLight);
            btn5 = CreateButton("5", btnNumBg, textLight);
            btn6 = CreateButton("6", btnNumBg, textLight);
            btn7 = CreateButton("7", btnNumBg, textLight);
            btn8 = CreateButton("8", btnNumBg, textLight);
            btn9 = CreateButton("9", btnNumBg, textLight);
            btnDecimal = CreateButton(".", btnNumBg, textLight);

            btnClear = CreateButton("C", btnFuncBg, Color.FromArgb(248, 113, 113)); // Soft Red Clear
            btnBackspace = CreateButton("⌫", btnFuncBg, btnFuncFg);
            btnPercent = CreateButton("%", btnFuncBg, btnFuncFg);
            btnSign = CreateButton("+/-", btnFuncBg, btnFuncFg);

            btnPlus = CreateButton("+", btnOpBg, Color.White);
            btnMinus = CreateButton("-", btnOpBg, Color.White);
            btnMultiply = CreateButton("x", btnOpBg, Color.White);
            btnDivide = CreateButton("÷", btnOpBg, Color.White);
            btnEquals = CreateButton("=", btnEqualsBg, Color.White);

            btn0.Click += NumberButton_Click;
            btn1.Click += NumberButton_Click;
            btn2.Click += NumberButton_Click;
            btn3.Click += NumberButton_Click;
            btn4.Click += NumberButton_Click;
            btn5.Click += NumberButton_Click;
            btn6.Click += NumberButton_Click;
            btn7.Click += NumberButton_Click;
            btn8.Click += NumberButton_Click;
            btn9.Click += NumberButton_Click;

            btnPlus.Click += OperatorButton_Click;
            btnMinus.Click += OperatorButton_Click;
            btnMultiply.Click += OperatorButton_Click;
            btnDivide.Click += OperatorButton_Click;

            btnClear.Click += btnClear_Click;
            btnDecimal.Click += btnDecimal_Click;
            btnEquals.Click += btnEquals_Click;
            btnBackspace.Click += btnBackspace_Click;
            btnSign.Click += btnSign_Click;
            btnPercent.Click += btnPercent_Click;

            int startX = 20, startY = 100;
            int btnW = 70, btnH = 55, gap = 8;

            btnClear.Location = new Point(startX, startY);
            btnBackspace.Location = new Point(startX + btnW + gap, startY);
            btnPercent.Location = new Point(startX + (btnW + gap) * 2, startY);
            btnDivide.Location = new Point(startX + (btnW + gap) * 3, startY);

            btn7.Location = new Point(startX, startY + btnH + gap);
            btn8.Location = new Point(startX + btnW + gap, startY + btnH + gap);
            btn9.Location = new Point(startX + (btnW + gap) * 2, startY + btnH + gap);
            btnMultiply.Location = new Point(startX + (btnW + gap) * 3, startY + btnH + gap);

            btn4.Location = new Point(startX, startY + (btnH + gap) * 2);
            btn5.Location = new Point(startX + btnW + gap, startY + (btnH + gap) * 2);
            btn6.Location = new Point(startX + (btnW + gap) * 2, startY + (btnH + gap) * 2);
            btnMinus.Location = new Point(startX + (btnW + gap) * 3, startY + (btnH + gap) * 2);

            btn1.Location = new Point(startX, startY + (btnH + gap) * 3);
            btn2.Location = new Point(startX + btnW + gap, startY + (btnH + gap) * 3);
            btn3.Location = new Point(startX + (btnW + gap) * 2, startY + (btnH + gap) * 3);
            btnPlus.Location = new Point(startX + (btnW + gap) * 3, startY + (btnH + gap) * 3);

            btnSign.Location = new Point(startX, startY + (btnH + gap) * 4);
            btn0.Location = new Point(startX + btnW + gap, startY + (btnH + gap) * 4);
            btnDecimal.Location = new Point(startX + (btnW + gap) * 2, startY + (btnH + gap) * 4);
            btnEquals.Location = new Point(startX + (btnW + gap) * 3, startY + (btnH + gap) * 4);

            this.Controls.AddRange(new Control[] {
                lblHistory, txtDisplay,
                btnClear, btnBackspace, btnPercent, btnDivide,
                btn7, btn8, btn9, btnMultiply,
                btn4, btn5, btn6, btnMinus,
                btn1, btn2, btn3, btnPlus,
                btnSign, btn0, btnDecimal, btnEquals
            });
        }

        private Button CreateButton(string text, Color bg, Color fg)
        {
            Button btn = new Button()
            {
                Text = text,
                Size = new Size(70, 55),
                BackColor = bg,
                ForeColor = fg,
                FlatStyle = FlatStyle.Flat,
                Font = new Font("Segoe UI", 12, FontStyle.Bold),
                Cursor = Cursors.Hand
            };
            btn.FlatAppearance.BorderSize = 0;
            return btn;
        }

        private void NumberButton_Click(object sender, EventArgs e)
        {
            Button button = (Button)sender;

            if (isCalculated)
            {
                txtDisplay.Text = button.Text;
                lblHistory.Text = "";
                isCalculated = false;
            }
            else if (txtDisplay.Text == "0")
            {
                txtDisplay.Text = button.Text;
            }
            else
            {
                txtDisplay.Text += button.Text;
            }
        }

        private void OperatorButton_Click(object sender, EventArgs e)
        {
            Button button = (Button)sender;

            if (isCalculated)
            {
                lblHistory.Text = "";
                isCalculated = false;
            }

            if (txtDisplay.Text.EndsWith(" "))
            {
                txtDisplay.Text = txtDisplay.Text.Substring(0, txtDisplay.Text.Length - 3) + $" {button.Text} ";
            }
            else
            {
                txtDisplay.Text += $" {button.Text} ";
            }
        }

        private void btnEquals_Click(object sender, EventArgs e)
        {
            try
            {
                if (string.IsNullOrWhiteSpace(txtDisplay.Text)) return;

                string rawExpression = txtDisplay.Text.Trim();

                //If expression ends with an unfulfilled operator, remove it before evaluating
                if (rawExpression.EndsWith("+") || rawExpression.EndsWith("-") || rawExpression.EndsWith("x") || rawExpression.EndsWith("÷"))
                {
                    rawExpression = rawExpression.Substring(0, rawExpression.Length - 1).Trim();
                }

                //Format string expression for DataTable parsing
                string parseableExpression = rawExpression.Replace("x", "*").Replace("÷", "/");

                //Handles PEMDAS automatically
                var evalResult = new DataTable().Compute(parseableExpression, null);

                double numericResult = Convert.ToDouble(evalResult);

                if (double.IsInfinity(numericResult) || double.IsNaN(numericResult))
                {
                    MessageBox.Show("Cannot divide by zero.", "Math Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
                    return;
                }

                lblHistory.Text = $"{rawExpression} =";
                txtDisplay.Text = numericResult.ToString();
                isCalculated = true;
            }
            catch (Exception)
            {
                MessageBox.Show("Invalid Mathematical Expression", "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
        }

        private void btnClear_Click(object sender, EventArgs e)
        {
            txtDisplay.Text = "0";
            lblHistory.Text = "";
            isCalculated = false;
        }

        private void btnBackspace_Click(object sender, EventArgs e)
        {
            if (isCalculated)
            {
                lblHistory.Text = "";
                return;
            }

            if (txtDisplay.Text.EndsWith(" "))
            {
                txtDisplay.Text = txtDisplay.Text.Substring(0, txtDisplay.Text.Length - 3);
            }
            else if (txtDisplay.Text.Length > 1)
            {
                txtDisplay.Text = txtDisplay.Text.Substring(0, txtDisplay.Text.Length - 1);
            }
            else
            {
                txtDisplay.Text = "0";
            }
        }

        private void btnDecimal_Click(object sender, EventArgs e)
        {
            if (isCalculated)
            {
                txtDisplay.Text = "0.";
                lblHistory.Text = "";
                isCalculated = false;
                return;
            }

            string[] tokens = txtDisplay.Text.Split(' ');
            string lastToken = tokens[tokens.Length - 1];

            if (!lastToken.Contains("."))
            {
                if (string.IsNullOrEmpty(lastToken))
                    txtDisplay.Text += "0.";
                else
                    txtDisplay.Text += ".";
            }
        }

        private void btnSign_Click(object sender, EventArgs e)
        {
            if (txtDisplay.Text == "0") return;

            string[] tokens = txtDisplay.Text.Split(' ');
            string lastToken = tokens[tokens.Length - 1];

            if (double.TryParse(lastToken, out double val))
            {
                val = -val;
                tokens[tokens.Length - 1] = val.ToString();
                txtDisplay.Text = string.Join(" ", tokens);
            }
        }

        private void btnPercent_Click(object sender, EventArgs e)
        {
            string[] tokens = txtDisplay.Text.Split(' ');
            string lastToken = tokens[tokens.Length - 1];

            if (double.TryParse(lastToken, out double val))
            {
                val = val / 100.0;
                tokens[tokens.Length - 1] = val.ToString();
                txtDisplay.Text = string.Join(" ", tokens);
            }
        }
    }
}