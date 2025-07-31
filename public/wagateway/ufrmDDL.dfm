object frmDDL: TfrmDDL
  Left = 0
  Top = 0
  Caption = 'DDL Table wa_outbox'
  ClientHeight = 441
  ClientWidth = 624
  Color = clWindow
  Ctl3D = False
  Font.Charset = DEFAULT_CHARSET
  Font.Color = clWindowText
  Font.Height = -12
  Font.Name = 'Segoe UI'
  Font.Style = []
  Position = poMainFormCenter
  TextHeight = 15
  object Memo1: TMemo
    Left = 8
    Top = 8
    Width = 608
    Height = 425
    Lines.Strings = (
      'CREATE TABLE `wa_outbox` ('
      '  `nomor` bigint(20) NOT NULL AUTO_INCREMENT,'
      '  `nowa` varchar(50) NOT NULL DEFAULT '#39#39','
      '  `pesan` text CHARACTER SET utf8 NOT NULL,'
      '  `tanggal_jam` datetime DEFAULT NULL,'
      
        '  `status` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '#39'ANTR' +
        'IAN'#39','
      '  `source` varchar(50) DEFAULT NULL,'
      
        '  `sender` varchar(10) NOT NULL DEFAULT '#39'NODEJS'#39' COMMENT '#39'ANY, N' +
        'ODEJS, QISCUS'#39','
      '  `success` varchar(1) DEFAULT NULL,'
      '  `response` text,'
      '  `request` text,'
      
        '  `type` varchar(10) NOT NULL DEFAULT '#39'TEXT'#39' COMMENT '#39'TEXT, IMAG' +
        'E, VIDEO'#39','
      '  `file` varchar(100) DEFAULT NULL,'
      '  PRIMARY KEY (`nomor`),'
      '  KEY `NOWA` (`nowa`),'
      '  KEY `STATUS` (`status`),'
      '  KEY `SENDER` (`sender`)'
      ') ENGINE=InnoDB AUTO_INCREMENT=146676 DEFAULT CHARSET=latin1'
      '')
    TabOrder = 0
  end
end
