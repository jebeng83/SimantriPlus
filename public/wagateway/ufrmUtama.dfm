object frmUtama: TfrmUtama
  Left = 0
  Top = 0
  Caption = 'WhatsApp Gateway'
  ClientHeight = 633
  ClientWidth = 953
  Color = clWindow
  Ctl3D = False
  Font.Charset = DEFAULT_CHARSET
  Font.Color = clWindowText
  Font.Height = -11
  Font.Name = 'Tahoma'
  Font.Style = []
  WindowState = wsMaximized
  OnCreate = FormCreate
  OnDestroy = FormDestroy
  OnShow = FormShow
  TextHeight = 13
  object Panel2: TPanel
    Left = 0
    Top = 0
    Width = 953
    Height = 614
    Align = alClient
    TabOrder = 0
    object PageControl1: TPageControl
      Left = 1
      Top = 1
      Width = 951
      Height = 612
      ActivePage = TabSheet3
      Align = alClient
      TabOrder = 0
      object TabSheet5: TTabSheet
        Caption = 'Konfigurasi'
        ImageIndex = 4
        object Label13: TLabel
          Left = 3
          Top = 226
          Width = 38
          Height = 13
          Caption = 'Interval'
        end
        object Label14: TLabel
          Left = 184
          Top = 225
          Width = 23
          Height = 13
          Caption = 'detik'
        end
        object Label18: TLabel
          Left = 3
          Top = 258
          Width = 52
          Height = 13
          Caption = 'Tipe Pesan'
        end
        object Label21: TLabel
          Left = 81
          Top = 258
          Width = 34
          Height = 13
          Caption = 'Sender'
        end
        object Label30: TLabel
          Left = 9
          Top = 379
          Width = 76
          Height = 13
          Caption = 'Telegram Token'
        end
        object GroupBox1: TGroupBox
          Left = 3
          Top = 3
          Width = 358
          Height = 214
          Caption = '  Database  '
          TabOrder = 0
          object Label7: TLabel
            Left = 16
            Top = 32
            Width = 22
            Height = 13
            Caption = 'Host'
          end
          object Label8: TLabel
            Left = 16
            Top = 57
            Width = 20
            Height = 13
            Caption = 'Port'
          end
          object Label9: TLabel
            Left = 16
            Top = 82
            Width = 48
            Height = 13
            Caption = 'Username'
          end
          object Label10: TLabel
            Left = 16
            Top = 107
            Width = 46
            Height = 13
            Caption = 'Password'
          end
          object Label11: TLabel
            Left = 16
            Top = 132
            Width = 46
            Height = 13
            Caption = 'Database'
          end
          object btnDDLInfo: TButton
            Left = 272
            Top = 176
            Width = 75
            Height = 25
            Caption = 'DDL Info'
            TabOrder = 0
            OnClick = btnDDLInfoClick
          end
          object edHost: TEdit
            Left = 70
            Top = 29
            Width = 121
            Height = 19
            TabOrder = 1
          end
          object edPort: TEdit
            Left = 70
            Top = 54
            Width = 121
            Height = 19
            TabOrder = 2
          end
          object edUser: TEdit
            Left = 70
            Top = 79
            Width = 121
            Height = 19
            TabOrder = 3
          end
          object edPass: TEdit
            Left = 70
            Top = 104
            Width = 121
            Height = 19
            PasswordChar = '*'
            TabOrder = 4
          end
          object edDatabase: TEdit
            Left = 70
            Top = 129
            Width = 121
            Height = 19
            TabOrder = 5
          end
          object btnConnect: TButton
            Left = 191
            Top = 176
            Width = 75
            Height = 25
            Caption = 'Connect'
            TabOrder = 6
            OnClick = btnConnectClick
          end
        end
        object edIntervalNodeJS: TEdit
          Left = 57
          Top = 223
          Width = 121
          Height = 19
          TabOrder = 1
          OnChange = edIntervalNodeJSChange
        end
        object cbCUSNodeJS: TCheckBox
          Left = 3
          Top = 277
          Width = 97
          Height = 17
          Caption = '@c.us'
          Checked = True
          State = cbChecked
          TabOrder = 2
          OnClick = cbCUSNodeJSClick
        end
        object cbGUSNodeJS: TCheckBox
          Left = 3
          Top = 300
          Width = 97
          Height = 17
          Caption = '@g.us'
          Checked = True
          State = cbChecked
          TabOrder = 3
          OnClick = cbGUSNodeJSClick
        end
        object cbNodeJSNodeJS: TCheckBox
          Left = 81
          Top = 277
          Width = 97
          Height = 17
          Caption = 'NODEJS'
          Checked = True
          State = cbChecked
          TabOrder = 4
          OnClick = cbNodeJSNodeJSClick
        end
        object cbQISCUSNodeJS: TCheckBox
          Left = 81
          Top = 300
          Width = 97
          Height = 17
          Caption = 'QISCUS'
          TabOrder = 5
          OnClick = cbQISCUSNodeJSClick
        end
        object GroupBox2: TGroupBox
          Left = 184
          Top = 258
          Width = 185
          Height = 105
          Caption = '         Sistem Modulus '
          TabOrder = 6
          object Label12: TLabel
            Left = 9
            Top = 28
            Width = 76
            Height = 13
            Caption = 'Faktor Modulus '
          end
          object Label25: TLabel
            Left = 9
            Top = 61
            Width = 64
            Height = 13
            Caption = 'Nilai Modulus '
          end
          object edFaktorModulus: TEdit
            Left = 88
            Top = 26
            Width = 89
            Height = 19
            TabOrder = 0
            OnChange = edFaktorModulusChange
          end
          object edNilaiModulus: TEdit
            Left = 88
            Top = 59
            Width = 89
            Height = 19
            TabOrder = 1
            OnChange = edNilaiModulusChange
          end
        end
        object cbModulusSystem: TCheckBox
          Left = 198
          Top = 257
          Width = 17
          Height = 17
          TabOrder = 7
          OnClick = cbModulusSystemClick
        end
        object edTeleToken: TEdit
          Left = 91
          Top = 377
          Width = 278
          Height = 19
          TabOrder = 8
          OnChange = edTeleTokenChange
        end
        object GroupBox3: TGroupBox
          Left = 11
          Top = 402
          Width = 358
          Height = 115
          Caption = '  Test Kirim Telegram  '
          TabOrder = 9
          object Label31: TLabel
            Left = 16
            Top = 32
            Width = 58
            Height = 13
            Caption = 'Telegram ID'
          end
          object Label32: TLabel
            Left = 16
            Top = 57
            Width = 22
            Height = 13
            Caption = 'Text'
          end
          object edTeleID: TEdit
            Left = 80
            Top = 29
            Width = 121
            Height = 19
            TabOrder = 0
            Text = '276444676'
          end
          object edTextTele: TEdit
            Left = 80
            Top = 54
            Width = 259
            Height = 19
            TabOrder = 1
            Text = '0xF0 0x9F 0x8F 0xA5 RS IYEM SEHAT SEJAHTERA'
          end
          object btnTestKirimTele: TButton
            Left = 264
            Top = 79
            Width = 75
            Height = 25
            Caption = 'Test Kirim'
            TabOrder = 2
            OnClick = btnTestKirimTeleClick
          end
        end
      end
      object TabSheet2: TTabSheet
        Caption = 'Test Kirim'
        ImageIndex = 1
        DesignSize = (
          943
          584)
        object Label1: TLabel
          Left = 16
          Top = 48
          Width = 31
          Height = 13
          Caption = 'Nomor'
        end
        object Label2: TLabel
          Left = 16
          Top = 96
          Width = 76
          Height = 13
          Caption = 'Pesan / Caption'
        end
        object Label3: TLabel
          Left = 16
          Top = 5
          Width = 20
          Height = 13
          Caption = 'Tipe'
        end
        object Label19: TLabel
          Left = 16
          Top = 224
          Width = 16
          Height = 13
          Caption = 'File'
        end
        object btnTestKirim: TButton
          Left = 635
          Top = 543
          Width = 112
          Height = 25
          Anchors = [akRight, akBottom]
          Caption = 'Test Kirim Pesan'
          TabOrder = 0
          OnClick = btnTestKirimClick
        end
        object btnStartTestKirim: TButton
          Left = 417
          Top = 543
          Width = 75
          Height = 25
          Anchors = [akRight, akBottom]
          Caption = 'START'
          TabOrder = 1
          OnClick = btnStartTestKirimClick
        end
        object edNomorTestKirim: TEdit
          Left = 16
          Top = 67
          Width = 313
          Height = 19
          TabOrder = 2
          Text = '62811119956@c.us'
        end
        object memPesanTestKirim: TMemo
          Left = 16
          Top = 112
          Width = 313
          Height = 89
          Lines.Strings = (
            '============================'
            '0xF0 0x9F 0x8F 0xA5 RS IYEM SEHAT SEJAHTERA'
            '0xF0 0x9F 0x93 0x9E 0218899023'
            '0xF0 0x9F 0x91 0x8B Halo! 0xF0 0x9F 0x98 0x8A'
            '============================'
            '0xF0 0x9F 0x93 0x84 *BUKTI REGISTER PENDAFTARAN - '
            'ANTRIAN POLI*'
            '0xF0 0x9F 0x93 0x85 Tanggal : 03-02-2005  05:47:23'
            '0xF0 0x9F 0x86 0x94 No Rawat : 202502-0045'
            '0xF0 0x9F 0x93 0x9D No RM : 001-74-09'
            '0xF0 0x9F 0x91 0xA4 Nama : Muhammad Wira Sableng'
            '0xF0 0x9F 0x8E 0x82 Tanggal Lahir : 18-12-1983'
            '0xE2 0x9A 0xA5 0x00 JK : Laki-Laki'
            '0xF0 0x9F 0x8F 0xA0 Alamat : Jln Kebun Sawit No. 23 '
            'Lampung'
            '0xF0 0x9F 0x8F 0xA5 Poli : Poliklinik Jantung'
            '0xF0 0x9F 0x91 0xA8 Dokter : dr. Salim Mulyana'
            '0xF0 0x9F 0x92 0xB3 Cara bayar : QRIS BCA'
            '0xF0 0x9F 0x94 0xA2 No Antri Poli : G-034'
            '============================'
            'Sumber Emot : https://www.fileformat.info/')
          TabOrder = 3
        end
        object cbTypeTestKirim: TComboBox
          Left = 16
          Top = 21
          Width = 145
          Height = 21
          ItemIndex = 0
          TabOrder = 4
          Text = 'japri'
          Items.Strings = (
            'japri'
            'group')
        end
        object btnAuthTestKirim: TButton
          Left = 501
          Top = 543
          Width = 122
          Height = 25
          Anchors = [akRight, akBottom]
          Caption = 'AUTHENTICATION'
          TabOrder = 5
          OnClick = btnAuthTestKirimClick
        end
        object edFileTestKirim: TEdit
          Left = 16
          Top = 243
          Width = 313
          Height = 19
          ReadOnly = True
          TabOrder = 6
        end
        object btnPilihFileTestKirim: TButton
          Left = 16
          Top = 268
          Width = 75
          Height = 25
          Caption = 'Pilih File'
          TabOrder = 7
          OnClick = btnPilihFileTestKirimClick
        end
        object btnTestKirimFile: TButton
          Left = 757
          Top = 543
          Width = 112
          Height = 25
          Anchors = [akRight, akBottom]
          Caption = 'Test Kirim File'
          TabOrder = 8
          OnClick = btnTestKirimFileClick
        end
      end
      object tabQRCode: TTabSheet
        Caption = '   QRCode   '
        DesignSize = (
          943
          584)
        object Label5: TLabel
          Left = 11
          Top = 560
          Width = 43
          Height = 13
          Anchors = [akLeft, akBottom]
          Caption = 'NPM URL'
          ExplicitTop = 506
        end
        object lblStatusQRCode: TLabel
          Left = 11
          Top = 404
          Width = 176
          Height = 25
          Caption = 'lblStatusQRCode'
          Font.Charset = DEFAULT_CHARSET
          Font.Color = clWindowText
          Font.Height = -21
          Font.Name = 'Tahoma'
          Font.Style = [fsBold]
          ParentFont = False
        end
        object edNPMURL: TEdit
          Left = 63
          Top = 557
          Width = 867
          Height = 19
          Anchors = [akLeft, akRight, akBottom]
          ReadOnly = True
          TabOrder = 0
          OnChange = edNPMURLChange
        end
        object Panel1: TPanel
          Left = 11
          Top = 16
          Width = 382
          Height = 382
          BevelOuter = bvNone
          BorderStyle = bsSingle
          TabOrder = 1
          object imgQRCode: TImage
            Left = 16
            Top = 19
            Width = 345
            Height = 345
            Stretch = True
          end
        end
        object btnStartQRCode: TButton
          Left = 855
          Top = 526
          Width = 75
          Height = 25
          Anchors = [akRight, akBottom]
          Caption = 'START'
          TabOrder = 2
          OnClick = btnStartTestKirimClick
        end
        object btnSetPort: TButton
          Left = 679
          Top = 526
          Width = 75
          Height = 25
          Anchors = [akRight, akBottom]
          Caption = 'Set Port'
          TabOrder = 3
          OnClick = btnSetPortClick
        end
        object btnGetPort: TButton
          Left = 768
          Top = 526
          Width = 75
          Height = 25
          Anchors = [akRight, akBottom]
          Caption = 'Get Port'
          TabOrder = 4
          OnClick = btnGetPortClick
        end
        object btnResetNodeJS: TButton
          Left = 536
          Top = 526
          Width = 130
          Height = 25
          Anchors = [akRight, akBottom]
          Caption = 'Reset Session NodeJS'
          TabOrder = 5
          OnClick = btnResetNodeJSClick
        end
        object cbAutoSwitch: TCheckBox
          Left = 11
          Top = 454
          Width = 270
          Height = 17
          Caption = 'Jalankan QISCUS otomatis jika NodeJS Error '
          TabOrder = 6
          OnClick = cbAutoSwitchClick
        end
        object cbAutoRun: TCheckBox
          Left = 11
          Top = 477
          Width = 270
          Height = 17
          Caption = 'Auto Run NodeJS Saat Aplikasi dibuka'
          TabOrder = 7
          OnClick = cbAutoRunClick
        end
        object cbAutoStart: TCheckBox
          Left = 11
          Top = 500
          Width = 270
          Height = 17
          Caption = 'Auto Start Gateway Saat NodeJS Ready'
          TabOrder = 8
          OnClick = cbAutoStartClick
        end
        object cbStopNodeJS: TCheckBox
          Left = 11
          Top = 432
          Width = 270
          Height = 17
          Caption = 'Stop NodeJS jika terjadi error'
          TabOrder = 9
          OnClick = cbStopNodeJSClick
        end
        object btnJumlahAntrian: TButton
          Left = 416
          Top = 526
          Width = 106
          Height = 25
          Anchors = [akRight, akBottom]
          Caption = 'Jumlah Antrian'
          TabOrder = 10
          OnClick = btnJumlahAntrianClick
        end
        object cbDescMessage: TCheckBox
          Left = 11
          Top = 523
          Width = 270
          Height = 17
          Caption = 'Proses Pesan dr yang terbaru (Descendeing)'
          TabOrder = 11
          OnClick = cbDescMessageClick
        end
      end
      object TabSheet3: TTabSheet
        Caption = 'Log'
        ImageIndex = 2
        DesignSize = (
          943
          584)
        object memLog: TMemo
          Left = 3
          Top = 6
          Width = 943
          Height = 544
          Anchors = [akLeft, akTop, akRight, akBottom]
          ScrollBars = ssVertical
          TabOrder = 0
        end
        object btnStartNodeJS: TButton
          Left = 667
          Top = 556
          Width = 135
          Height = 25
          Anchors = [akRight, akBottom]
          Caption = 'START NODEJS'
          TabOrder = 1
          OnClick = btnStartTestKirimClick
        end
        object btnStartQISCUS: TButton
          Left = 808
          Top = 556
          Width = 135
          Height = 25
          Anchors = [akRight, akBottom]
          Caption = 'START QISCUS'
          TabOrder = 2
          OnClick = btnRunQISCUSClick
        end
        object btnLastQuery: TButton
          Left = 568
          Top = 556
          Width = 93
          Height = 25
          Anchors = [akRight, akBottom]
          Caption = 'LAST QUERY'
          TabOrder = 3
          OnClick = btnLastQueryClick
        end
      end
      object TabSheet4: TTabSheet
        Caption = 'NPM DOSCOM'
        ImageIndex = 3
        DesignSize = (
          943
          584)
        object Label4: TLabel
          Left = 3
          Top = 563
          Width = 59
          Height = 13
          Anchors = [akLeft, akBottom]
          Caption = 'WA API App'
          ExplicitTop = 509
        end
        object Label6: TLabel
          Left = 3
          Top = 538
          Width = 55
          Height = 13
          Anchors = [akLeft, akBottom]
          Caption = 'Nodejs App'
          ExplicitTop = 484
        end
        object memDOSCOM: TMemo
          Left = 0
          Top = 8
          Width = 943
          Height = 512
          Anchors = [akLeft, akTop, akRight, akBottom]
          Color = clBlack
          Font.Charset = ANSI_CHARSET
          Font.Color = clWhite
          Font.Height = -13
          Font.Name = 'Courier New'
          Font.Style = [fsBold]
          ParentFont = False
          ScrollBars = ssVertical
          TabOrder = 0
        end
        object edWAAPIApp: TEdit
          Left = 71
          Top = 560
          Width = 727
          Height = 19
          Anchors = [akLeft, akRight, akBottom]
          TabOrder = 1
        end
        object btnBrowseWAAPIApp: TButton
          Left = 804
          Top = 557
          Width = 59
          Height = 25
          Anchors = [akRight, akBottom]
          Caption = '. . . .'
          TabOrder = 2
          OnClick = btnBrowseWAAPIAppClick
        end
        object btnRUNNodeJS: TButton
          Left = 868
          Top = 557
          Width = 75
          Height = 25
          Anchors = [akRight, akBottom]
          Caption = 'RUN'
          TabOrder = 3
          OnClick = btnRUNNodeJSClick
        end
        object edNodeJSApp: TEdit
          Left = 71
          Top = 532
          Width = 791
          Height = 19
          Anchors = [akLeft, akRight, akBottom]
          ReadOnly = True
          TabOrder = 4
        end
        object btnBrowseNodejsApp: TButton
          Left = 868
          Top = 526
          Width = 75
          Height = 25
          Anchors = [akRight, akBottom]
          Caption = '. . . .'
          TabOrder = 5
          OnClick = btnBrowseNodejsAppClick
        end
      end
      object TabSheet6: TTabSheet
        Caption = '   Web WA   '
        ImageIndex = 5
        object webWA: TEdgeBrowser
          Left = 0
          Top = 0
          Width = 943
          Height = 543
          Align = alClient
          TabOrder = 0
          AllowSingleSignOnUsingOSPrimaryAccount = False
          TargetCompatibleBrowserVersion = '117.0.2045.28'
          UserDataFolder = '%LOCALAPPDATA%\bds.exe.WebView2'
        end
        object Panel3: TPanel
          Left = 0
          Top = 543
          Width = 943
          Height = 41
          Align = alBottom
          TabOrder = 1
          DesignSize = (
            943
            41)
          object btnResetWebWA: TButton
            Left = 720
            Top = 9
            Width = 213
            Height = 25
            Anchors = [akRight, akBottom]
            Caption = 'Reset Session Web WA'
            TabOrder = 0
            OnClick = btnResetWebWAClick
          end
        end
      end
      object tabQiscus: TTabSheet
        Caption = '   Qiscus   '
        ImageIndex = 6
        object Label15: TLabel
          Left = 16
          Top = 8
          Width = 31
          Height = 13
          Caption = 'Nomor'
        end
        object Label16: TLabel
          Left = 16
          Top = 56
          Width = 29
          Height = 13
          Caption = 'Pesan'
        end
        object Label17: TLabel
          Left = 16
          Top = 223
          Width = 35
          Height = 13
          Caption = 'Aplikasi'
        end
        object Label20: TLabel
          Left = 16
          Top = 315
          Width = 34
          Height = 13
          Caption = 'Sender'
        end
        object Label22: TLabel
          Left = 99
          Top = 315
          Width = 52
          Height = 13
          Caption = 'Tipe Pesan'
        end
        object Label23: TLabel
          Left = 17
          Top = 386
          Width = 38
          Height = 13
          Caption = 'Interval'
        end
        object Label24: TLabel
          Left = 216
          Top = 386
          Width = 23
          Height = 13
          Caption = 'detik'
        end
        object Label26: TLabel
          Left = 17
          Top = 411
          Width = 44
          Height = 13
          Caption = 'Template'
        end
        object Label27: TLabel
          Left = 17
          Top = 436
          Width = 66
          Height = 13
          Caption = 'Jumlah Param'
        end
        object Label28: TLabel
          Left = 17
          Top = 461
          Width = 45
          Height = 13
          Caption = 'Qiscus ID'
        end
        object Label29: TLabel
          Left = 17
          Top = 486
          Width = 52
          Height = 13
          Caption = 'Qiscus Key'
        end
        object edNomorQISCUS: TEdit
          Left = 16
          Top = 27
          Width = 313
          Height = 19
          TabOrder = 0
          Text = '62811119956@c.us'
        end
        object memPesanQISCUS: TMemo
          Left = 16
          Top = 72
          Width = 313
          Height = 137
          Lines.Strings = (
            'Test Kirim pesan')
          ScrollBars = ssBoth
          TabOrder = 1
        end
        object edAppQISCUS: TEdit
          Left = 16
          Top = 242
          Width = 313
          Height = 19
          TabOrder = 2
          Text = 'TEST APP'
        end
        object btnTestKirimQISCUS: TButton
          Left = 16
          Top = 277
          Width = 112
          Height = 25
          Caption = 'Test Kirim Pesan'
          TabOrder = 3
          OnClick = btnTestKirimQISCUSClick
        end
        object btnRunQISCUS: TButton
          Left = 134
          Top = 277
          Width = 112
          Height = 25
          Caption = 'RUN'
          TabOrder = 4
          OnClick = btnRunQISCUSClick
        end
        object cbNodeJSQiscus: TCheckBox
          Left = 16
          Top = 334
          Width = 97
          Height = 17
          Caption = 'NODEJS'
          TabOrder = 5
          OnClick = cbNodeJSQiscusClick
        end
        object cbQiscusQiscus: TCheckBox
          Left = 16
          Top = 357
          Width = 97
          Height = 17
          Caption = 'QISCUS'
          Checked = True
          State = cbChecked
          TabOrder = 6
          OnClick = cbQiscusQiscusClick
        end
        object cbCUSQISCUS: TCheckBox
          Left = 99
          Top = 334
          Width = 97
          Height = 17
          Caption = '@c.us'
          Checked = True
          State = cbChecked
          TabOrder = 7
          OnClick = cbCUSQISCUSClick
        end
        object cbGUSQISCUS: TCheckBox
          Left = 99
          Top = 357
          Width = 97
          Height = 17
          Caption = '@g.us'
          TabOrder = 8
          OnClick = cbGUSQISCUSClick
        end
        object edIntervalQISCUS: TEdit
          Left = 89
          Top = 384
          Width = 121
          Height = 19
          TabOrder = 9
          OnChange = edIntervalQISCUSChange
        end
        object edTemplateQiscus: TEdit
          Left = 89
          Top = 409
          Width = 258
          Height = 19
          TabOrder = 10
          OnChange = edTemplateQiscusChange
        end
        object edParamQiscus: TEdit
          Left = 89
          Top = 434
          Width = 121
          Height = 19
          TabOrder = 11
          OnChange = edParamQiscusChange
        end
        object edQiscusID: TEdit
          Left = 89
          Top = 459
          Width = 258
          Height = 19
          TabOrder = 12
          OnChange = edQiscusIDChange
        end
        object edQiscusKey: TEdit
          Left = 89
          Top = 484
          Width = 258
          Height = 19
          TabOrder = 13
          OnChange = edQiscusKeyChange
        end
        object cbAutaStartQiscus: TCheckBox
          Left = 17
          Top = 521
          Width = 270
          Height = 17
          Caption = 'Auto Start Qiscus Saat Program dijalankan'
          TabOrder = 14
          OnClick = cbAutaStartQiscusClick
        end
      end
    end
  end
  object StatusBar1: TStatusBar
    Left = 0
    Top = 614
    Width = 953
    Height = 19
    Panels = <
      item
        Width = 500
      end
      item
        Width = 300
      end
      item
        Text = 'Database : not connected'
        Width = 300
      end>
  end
  object http: TNetHTTPClient
    UserAgent = 'Embarcadero URI Client/1.0'
    Left = 421
    Top = 73
  end
  object conn: TFDConnection
    Params.Strings = (
      'Database=ehr'
      'Password=rspon2014'
      'User_Name=sirs'
      'Server=172.16.1.12'
      'DriverID=MySQL')
    Left = 533
    Top = 137
  end
  object zqr: TFDQuery
    Connection = conn
    Left = 477
    Top = 137
  end
  object timNodeJS: TTimer
    Enabled = False
    Interval = 100
    OnTimer = timNodeJSTimer
    Left = 421
    Top = 137
  end
  object dosComm: TDosCommand
    InputToOutput = False
    MaxTimeAfterBeginning = 0
    MaxTimeAfterLastOutput = 0
    OnNewLine = dosCommNewLine
    Left = 421
    Top = 201
  end
  object OpenFIle: TOpenDialog
    Left = 573
    Top = 241
  end
  object timQiscus: TTimer
    Enabled = False
    OnTimer = timQiscusTimer
    Left = 693
    Top = 113
  end
  object httpQiscus: TNetHTTPClient
    UserAgent = 'Embarcadero URI Client/1.0'
    Left = 757
    Top = 73
  end
  object zqrQiscus: TFDQuery
    Connection = conn
    Left = 685
    Top = 185
  end
  object zqrGeneral: TFDQuery
    Connection = conn
    Left = 485
    Top = 233
  end
end
