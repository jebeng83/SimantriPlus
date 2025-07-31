object frmLastQuery: TfrmLastQuery
  Left = 0
  Top = 0
  Caption = 'LAST QUERY'
  ClientHeight = 441
  ClientWidth = 624
  Color = clBtnFace
  Font.Charset = DEFAULT_CHARSET
  Font.Color = clWindowText
  Font.Height = -12
  Font.Name = 'Segoe UI'
  Font.Style = []
  Position = poDesktopCenter
  OnShow = FormShow
  TextHeight = 15
  object memQuery: TMemo
    Left = 8
    Top = 16
    Width = 608
    Height = 385
    Lines.Strings = (
      'memQuery')
    TabOrder = 0
  end
  object btnTUtup: TButton
    Left = 544
    Top = 408
    Width = 72
    Height = 25
    Caption = 'TUTUP'
    TabOrder = 1
    OnClick = btnTUtupClick
  end
end
