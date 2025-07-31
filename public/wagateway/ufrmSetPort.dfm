object frmSetPort: TfrmSetPort
  Left = 0
  Top = 0
  Caption = 'Set Port'
  ClientHeight = 56
  ClientWidth = 274
  Color = clBtnFace
  Font.Charset = DEFAULT_CHARSET
  Font.Color = clWindowText
  Font.Height = -12
  Font.Name = 'Segoe UI'
  Font.Style = []
  Position = poMainFormCenter
  OnShow = FormShow
  TextHeight = 15
  object Label1: TLabel
    Left = 8
    Top = 16
    Width = 22
    Height = 15
    Caption = 'Port'
  end
  object edPort: TEdit
    Left = 48
    Top = 13
    Width = 89
    Height = 23
    TabOrder = 0
    Text = 'edPort'
  end
  object btnSetPort: TButton
    Left = 152
    Top = 12
    Width = 97
    Height = 25
    Caption = 'Set Port'
    TabOrder = 1
    OnClick = btnSetPortClick
  end
end
