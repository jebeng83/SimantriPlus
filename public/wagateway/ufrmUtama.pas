unit ufrmUtama;

interface

uses
  Winapi.Windows, Winapi.Messages, System.SysUtils, System.Variants, System.Classes, Vcl.Graphics,
  Vcl.Controls, Vcl.Forms, Vcl.Dialogs, Vcl.StdCtrls, Vcl.Imaging.pngimage,
  Vcl.ExtCtrls, WebView2, Winapi.ActiveX, Vcl.Edge, Vcl.ComCtrls, Vcl.OleCtrls,
  SHDocVw, System.Net.URLClient, System.Net.HttpClient,
  System.Net.HttpClientComponent, FireDAC.Stan.Intf, FireDAC.Stan.Option,
  FireDAC.Stan.Error, FireDAC.UI.Intf, FireDAC.Phys.Intf, FireDAC.Stan.Def,
  FireDAC.Stan.Pool, FireDAC.Stan.Async, FireDAC.Phys, FireDAC.Phys.MySQL,
  FireDAC.Phys.MySQLDef, FireDAC.VCLUI.Wait, Data.DB, FireDAC.Comp.Client,
  FireDAC.Stan.Param, FireDAC.DatS, FireDAC.DApt.Intf, FireDAC.DApt,
  FireDAC.Comp.DataSet, DosCommand, INIFiles;

type
  TExtractMessage = record
    App: String;
    Msg: String;
  end;

  TSendQiscus = record
    request: String;
    response: String;
  end;

  TSuccessType = (stError, stSuccess, stFailed);

  TSendMessageStatus = record
    Success: TSuccessType;
    Request: String;
    Response: String;
  end;

  TfrmUtama = class(TForm)
    Panel2: TPanel;
    StatusBar1: TStatusBar;
    PageControl1: TPageControl;
    tabQRCode: TTabSheet;
    TabSheet2: TTabSheet;
    TabSheet3: TTabSheet;
    memLog: TMemo;
    btnTestKirim: TButton;
    http: TNetHTTPClient;
    conn: TFDConnection;
    zqr: TFDQuery;
    timNodeJS: TTimer;
    btnStartTestKirim: TButton;
    Label1: TLabel;
    edNomorTestKirim: TEdit;
    btnStartNodeJS: TButton;
    memPesanTestKirim: TMemo;
    Label2: TLabel;
    Label3: TLabel;
    cbTypeTestKirim: TComboBox;
    TabSheet4: TTabSheet;
    dosComm: TDosCommand;
    memDOSCOM: TMemo;
    Label4: TLabel;
    edWAAPIApp: TEdit;
    btnBrowseWAAPIApp: TButton;
    Label5: TLabel;
    edNPMURL: TEdit;
    btnRUNNodeJS: TButton;
    TabSheet5: TTabSheet;
    Label6: TLabel;
    edNodeJSApp: TEdit;
    btnBrowseNodejsApp: TButton;
    GroupBox1: TGroupBox;
    btnDDLInfo: TButton;
    Label7: TLabel;
    edHost: TEdit;
    Label8: TLabel;
    edPort: TEdit;
    Label9: TLabel;
    edUser: TEdit;
    edPass: TEdit;
    Label10: TLabel;
    Label11: TLabel;
    edDatabase: TEdit;
    btnConnect: TButton;
    btnAuthTestKirim: TButton;
    TabSheet6: TTabSheet;
    webWA: TEdgeBrowser;
    Panel1: TPanel;
    imgQRCode: TImage;
    OpenFIle: TOpenDialog;
    lblStatusQRCode: TLabel;
    edIntervalNodeJS: TEdit;
    Label13: TLabel;
    Label14: TLabel;
    tabQiscus: TTabSheet;
    timQiscus: TTimer;
    Label15: TLabel;
    edNomorQISCUS: TEdit;
    Label16: TLabel;
    memPesanQISCUS: TMemo;
    Label17: TLabel;
    edAppQISCUS: TEdit;
    btnTestKirimQISCUS: TButton;
    httpQiscus: TNetHTTPClient;
    btnRunQISCUS: TButton;
    zqrQiscus: TFDQuery;
    Label18: TLabel;
    cbCUSNodeJS: TCheckBox;
    cbGUSNodeJS: TCheckBox;
    Label19: TLabel;
    edFileTestKirim: TEdit;
    btnPilihFileTestKirim: TButton;
    btnTestKirimFile: TButton;
    cbNodeJSQiscus: TCheckBox;
    Label20: TLabel;
    cbQiscusQiscus: TCheckBox;
    Label21: TLabel;
    cbNodeJSNodeJS: TCheckBox;
    cbQISCUSNodeJS: TCheckBox;
    Label22: TLabel;
    cbCUSQISCUS: TCheckBox;
    cbGUSQISCUS: TCheckBox;
    btnStartQRCode: TButton;
    Label23: TLabel;
    edIntervalQISCUS: TEdit;
    Label24: TLabel;
    Panel3: TPanel;
    btnResetWebWA: TButton;
    btnStartQISCUS: TButton;
    btnSetPort: TButton;
    btnGetPort: TButton;
    btnResetNodeJS: TButton;
    GroupBox2: TGroupBox;
    cbModulusSystem: TCheckBox;
    Label12: TLabel;
    edFaktorModulus: TEdit;
    Label25: TLabel;
    edNilaiModulus: TEdit;
    cbAutoSwitch: TCheckBox;
    cbAutoRun: TCheckBox;
    cbAutoStart: TCheckBox;
    Label26: TLabel;
    edTemplateQiscus: TEdit;
    Label27: TLabel;
    edParamQiscus: TEdit;
    Label28: TLabel;
    edQiscusID: TEdit;
    Label29: TLabel;
    edQiscusKey: TEdit;
    Label30: TLabel;
    edTeleToken: TEdit;
    GroupBox3: TGroupBox;
    Label31: TLabel;
    Label32: TLabel;
    edTeleID: TEdit;
    edTextTele: TEdit;
    btnTestKirimTele: TButton;
    cbAutaStartQiscus: TCheckBox;
    cbStopNodeJS: TCheckBox;
    btnJumlahAntrian: TButton;
    zqrGeneral: TFDQuery;
    cbDescMessage: TCheckBox;
    btnLastQuery: TButton;
    procedure btnAuthTestKirimClick(Sender: TObject);
    procedure btnRunQISCUSClick(Sender: TObject);
    procedure FormDestroy(Sender: TObject);
    procedure FormCreate(Sender: TObject);
    procedure btnTestKirimClick(Sender: TObject);
    procedure btnStartTestKirimClick(Sender: TObject);
    procedure btnBrowseWAAPIAppClick(Sender: TObject);
    procedure btnTestKirimQISCUSClick(Sender: TObject);
    procedure btnRUNNodeJSClick(Sender: TObject);
    procedure btnBrowseNodejsAppClick(Sender: TObject);
    procedure btnDDLInfoClick(Sender: TObject);
    procedure btnConnectClick(Sender: TObject);
    procedure dosCommNewLine(ASender: TObject; const ANewLine: string; AOutputType:
        TOutputType);
    procedure edIntervalNodeJSChange(Sender: TObject);
    procedure edNPMURLChange(Sender: TObject);
    function sendMessage(number: String; Message: String; tipe: String='japri'): TSendMessageStatus;
    procedure timNodeJSTimer(Sender: TObject);
    procedure timQiscusTimer(Sender: TObject);
    procedure writeLog(AText, AKode: String);
    procedure btnPilihFileTestKirimClick(Sender: TObject);
    procedure btnTestKirimFileClick(Sender: TObject);
    procedure edIntervalQISCUSChange(Sender: TObject);
    procedure btnResetWebWAClick(Sender: TObject);
    procedure cbNodeJSQiscusClick(Sender: TObject);
    procedure cbQiscusQiscusClick(Sender: TObject);
    procedure cbCUSQISCUSClick(Sender: TObject);
    procedure cbGUSQISCUSClick(Sender: TObject);
    procedure cbCUSNodeJSClick(Sender: TObject);
    procedure cbGUSNodeJSClick(Sender: TObject);
    procedure cbNodeJSNodeJSClick(Sender: TObject);
    procedure cbQISCUSNodeJSClick(Sender: TObject);
    procedure btnGetPortClick(Sender: TObject);
    procedure btnJumlahAntrianClick(Sender: TObject);
    procedure btnLastQueryClick(Sender: TObject);
    procedure btnResetNodeJSClick(Sender: TObject);
    procedure btnSetPortClick(Sender: TObject);
    procedure btnTestKirimTeleClick(Sender: TObject);
    procedure cbAutaStartQiscusClick(Sender: TObject);
    procedure cbAutoRunClick(Sender: TObject);
    procedure cbAutoStartClick(Sender: TObject);
    procedure cbAutoSwitchClick(Sender: TObject);
    procedure cbDescMessageClick(Sender: TObject);
    procedure cbModulusSystemClick(Sender: TObject);
    procedure cbStopNodeJSClick(Sender: TObject);
    procedure edFaktorModulusChange(Sender: TObject);
    procedure edNilaiModulusChange(Sender: TObject);
    procedure edParamQiscusChange(Sender: TObject);
    procedure edQiscusIDChange(Sender: TObject);
    procedure edQiscusKeyChange(Sender: TObject);
    procedure edTeleTokenChange(Sender: TObject);
    procedure edTemplateQiscusChange(Sender: TObject);
    procedure FormShow(Sender: TObject);
  private
  public
    procedure ConnectingDb;
    function  showQrCode(strCode: String; imgQr: TImage): boolean;
    function  SendQiscus(App, Nowa, msg: String): TSendQiscus;
    function  ExtractMessage(Msg, source: String): TExtractMessage;
    function  sendFile(number, Caption, FileName: String): TSendMessageStatus;
    function  ValidModulusValue(Nilai: String): boolean;
    function  ParseUTF8(AStr: String): String;
    function  SendTele(ATeleID, AText: String): TSendMessageStatus;
  end;

var
  frmUtama: TfrmUtama;
  isExec: Boolean;
  cnf: TIniFile;
  url: String;
  dos: STring;
  nod: String;
  pth: String;
  auth: Boolean;
  _version: String = '2025-05-24';
  lastDay: Integer;
  last_query: String;
  //kode write log = '0041';

implementation

uses
System.NetEncoding, System.UITypes, System.JSON, System.StrUtils, ufrmDDL,
DelphiZXIngQRCode, System.Math, ufrmSetPort, System.IOUtils;

{$R *.dfm}

function  TfrmUtama.SendTele(ATeleID, AText: String): TSendMessageStatus;
var
  lst: TStrings;
  url: string;
  stm: TStringSTream;
  jso: TJSONObject;
  sss: string;
begin
  url := 'https://api.telegram.org/bot' + edTeleToken.Text + '/sendMessage';
  lst := TStringList.Create;
  stm := TStringStream.Create;

  ATeleID := StringReplace(ATeleID, '@t.us', '', [rfReplaceAll]);

  lst.Values['chat_id']    := ATeleID;
  lst.Values['text']       := ParseUTF8(AText);
  lst.Values['parse_mode'] := 'html';

  result.Success := stError;
  result.Request := lst.Text;

  try
    http.Post(url, lst, stm);
    result.Response := stm.DataString;
    result.Success  := stSuccess;
  except
    on E:Exception do
    begin
      result.Success  := stError;
      result.Response := E.Message;
      exit;
    end;
  end;

  try
    jso := TJSONObject(TJSONObject.ParseJSONValue(result.Response));
  except
    on E:Exception do
    begin
      result.Success  := stError;
      result.Response := E.Message;
      exit;
    end;
  end;

  try
    sss := jso.Values['ok'].Value;
  except
    result.Success := stError;
    exit;
  end;

  if sss = 'true' then
  result.Success := stSuccess else
  result.Success := stFailed;
end;

function  TfrmUtama.ParseUTF8(AStr: String): String;
var
  xxx: Integer;
  sss: String;
  byt: array of byte;
  off: Integer;
  sel: String;
  chr: String;
begin
  xxx := 1;
  sss := '';
  sel := '  ';
  off := 0;

  while xxx <= length(AStr) do
  begin
    sel[1] := sel[2];
    sel[2] := AStr[xxx];

    if sel = '0x' then
    begin
      inc(off);
      chr := '';
      inc(xxx);
      chr := AStr[xxx];
      inc(xxx);
      chr := chr + AStr[xxx];

      if chr <> '00' then
      begin
        SetLength(byt, off);
        byt[off - 1] := strtoint('$' + chr);
      end;
    end;

    if off = 4 then
    begin
     sss := Copy(sss, 1, Length(sss) - 1);
     sss := sss + TEncoding.UTF8.GetString(byt);
    end;

    if off = 0 then
    sss := sss + AStr[xxx];

    if off = 4 then
    off := 0;

    inc(xxx);
  end;

  result := sss;
end;

function TfrmUtama.ValidModulusValue(Nilai: String): boolean;
var
  iii: Integer;
  lst: TStrings;
  xxx: Integer;
begin
  result   := true;
  lst      := TStringList.Create;
  lst.Text := StringReplace(Nilai, ',', #13#10, [rfReplaceAll]);

  for iii := 0 to lst.Count - 1 do
  begin
    xxx := StrToIntDef(trim(lst[iii]), -1);

    if xxx = -1 then
    begin
      result := false;
      lst.Free;
      exit;
    end;
  end;

  lst.free;
end;

function TfrmUtama.sendFile(number, Caption, FileName: String): TSendMessageStatus;
var
  lst: TStrings;
  stm: TStringStream;
  res: String;
  jso: TJSONObject;
  stt: String;
begin
  lst := TStringList.create;
  stm := TStringStream.Create;
  lst.Values['number']  := number;
  lst.Values['caption'] := Caption;
  lst.Values['namafile'] := FileName;

  result.Success  := stError;
  result.Response := '';
  result.Request  := StringReplace(lst.Text, '"', '\"', [rfReplaceAll]);

  try
    http.Post(url + '/send-file', lst, stm);
    res := stm.DataString;
  except
    on E:Exception do
    begin
      writeLog(E.Message, '0001');
      result.Response := StringReplace(E.Message, '"', '\"', [rfReplaceAll]);
      result.Success  := stError;
      lst.Free;
      stm.Free;
      exit;
    end;
  end;
  lst.Free;
  stm.Free;

  jso := TJSONObject(TJSONObject.ParseJSONValue(res));

  try
    stt := jso.Values['status'].Value;
  except
    on E:Exception do
    begin
      writeLog(E.Message, '0002');
      result.Response := StringReplace(E.Message, '"', '\"', [rfReplaceAll]);
      result.Success  := stError;
      exit;
    end;
  end;

  if stt = 'true' then
  result.Success := stSuccess else
  result.Success := stFailed;

  if result.Success <> stSuccess then
  writeLog(res, '0003');

  result.Response := StringReplace(res, '"', '\"', [rfReplaceAll]);;
end;

function TfrmUtama.showQrCode(strCode: String; imgQr: TImage): boolean;
var
  QRCode: TDelphiZXingQRCode;
  QRCodeBitmap: TBitmap;
  Row, Column: Integer;
begin
  QRCodeBitmap := TBitmap.Create;
  QRCode := TDelphiZXingQRCode.Create;
  try
    QRCode.Data := strCode;
    QRCode.Encoding := TQRCodeEncoding(0);
    QRCode.QuietZone := 0;
    QRCodeBitmap.SetSize(QRCode.Rows, QRCode.Columns);
    for Row := 0 to QRCode.Rows - 1 do
    begin
      for Column := 0 to QRCode.Columns - 1 do
      begin
        if (QRCode.IsBlack[Row, Column]) then
        begin
          QRCodeBitmap.Canvas.Pixels[Column, Row] := clBlack;
        end
        else
        begin
          QRCodeBitmap.Canvas.Pixels[Column, Row] :=  clWhite;
        end;
      end;
    end;
    Application.ProcessMessages;
    imgQr.Picture.Bitmap := QRCodeBitmap;
    Result := true;
  finally
    QRCode.Free;
    QRCodeBitmap.Free;
  end;
end;

procedure TfrmUtama.btnAuthTestKirimClick(Sender: TObject);
begin
  auth := true;
  StatusBar1.Panels[1].Text := 'Nodejs : AUTHENTICATED';
  MemLog.Clear;
end;

procedure TfrmUtama.btnRunQISCUSClick(Sender: TObject);
begin
  if btnRunQISCUS.Caption = 'RUN' then
  begin
    btnRunQISCUS.Caption   := 'STOP';
    timQiscus.Enabled      := true;
    btnStartQISCUS.Caption := 'STOP QISCUS';
  end else
  begin
    btnRunQISCUS.Caption   := 'RUN';
    timQiscus.Enabled      := false;
    btnStartQISCUS.Caption := 'START QISCUS';
  end;
end;

procedure TfrmUtama.btnPilihFileTestKirimClick(Sender: TObject);
var
  pth: String;
  fil: String;
begin
  if OpenFIle.Execute then
  begin
    edFileTestKirim.Text := OpenFile.FileName;
    pth := ExtractFilePath(paramstr(0));
    fil := ExtractFileName(edFileTestKirim.Text);
    CopyFile(PChar(edFileTestKirim.Text), PChar(pth + 'media\' + fil), FALSE);
  end;
end;

procedure TfrmUtama.btnTestKirimFileClick(Sender: TObject);
var
  sss: String;
begin
  sss := ExtractFileName(edFileTestKirim.Text);
  sendFile(edNomorTestKirim.Text, memPesanTestKirim.Text, sss);
end;

procedure TfrmUtama.btnResetWebWAClick(Sender: TObject);
begin
  WebWA.ReinitializeWebView;
end;

procedure TfrmUtama.FormDestroy(Sender: TObject);
begin
  cnf.Free;
  dosComm.stop;
end;


procedure TfrmUtama.writeLog(AText, AKode: String);
begin
  if AText = '' then
  memLog.Lines.Add('') else
  memLog.Lines.Add('[' + formatDateTime('YYYY-MM-dd HH:mm:ss', now)
     +' - ' + AKode + '] : ' +AText);
end;

procedure TfrmUtama.FormCreate(Sender: TObject);
var
  ini: String;
begin
  PageControl1.ActivePageIndex := 2;
  LastDay := trunc(now);
  Caption := Caption + ' [Version : ' + _version + ']';

  pth := ExtractFilePath(Application.ExeName);
  ini := pth + 'setting.ini';
  cnf := TINIFile.Create(ini);
  url := cnf.ReadString('general', 'npm_url', '');
  dos := cnf.ReadString('general', 'npm_path', '');
  nod := cnf.ReadString('general', 'node_path', '');
  auth:= false;

  edNPMURL.Text    := url;
  edWAAPIApp.Text  := dos;
  edNodeJSApp.Text := nod;

  //if (dos <> '') and (nod <> '') then
  //Button6Click(nil);

  StatusBar1.Panels[0].Text := ini;
  ConnectingDb;

  WebWA.Navigate('https://web.whatsapp.com/');
  PageControl1.ActivePageIndex := 4;
  lblStatusQRCode.Caption := '';
  edIntervalNodeJS.Text := cnf.ReadString('nodejs', 'interval', '10');
  edNPMURL.Text := cnf.ReadString('general', 'npm_url', 'http://localhost:8100');
  edIntervalQISCUS.Text := cnf.ReadString('qiscus', 'interval', '3');
  timNodeJS.Interval    := StrToIntDef(edIntervalNodeJS.Text, 10) * 1000;
  timQiscus.Interval    := StrToIntDef(edIntervalQISCUS.Text, 1) * 1000;
  cbAutoSwitch.Checked  := cnf.ReadString('general', 'autoswitch', 'false') = 'true';
  cbStopNodeJS.Checked  := cnf.ReadString('general', 'stopnodejs', 'false') = 'true';
  cbDescMessage.Checked := cnf.ReadString('general', 'descmessage', 'false') = 'true';
  WebWA.UserDataFolder  := pth + 'webWA_cache';
  WebWA.ReinitializeWebView;

  cbNodeJSQiscus.Checked    := cnf.ReadString('qiscus', 'nodejs', 'false') = 'true';
  cbQiscusQiscus.Checked    := cnf.ReadString('qiscus', 'qiscus', 'true') = 'true';
  cbCUSQISCUS.Checked       := cnf.ReadString('qiscus', 'cus', 'true') = 'true';
  cbGUSQISCUS.Checked       := cnf.ReadString('qiscus', 'gus', 'false') = 'true';
  cbAutaStartQiscus.Checked := cnf.ReadString('qiscus', 'autostart', 'false') = 'true';
  edTemplateQiscus.Text     := cnf.ReadString('qiscus', 'template', 'public_message_pendol2');
  edParamQiscus.Text        := cnf.ReadString('qiscus', 'param', '6');
  edQiscusID.Text           := cnf.ReadString('qiscus', 'id', '');
  edQiscusKey.Text          := cnf.ReadString('qiscus', 'key', '');

  cbCUSNodeJS.Checked    := cnf.ReadString('nodejs', 'cus', 'true') = 'true';
  cbGUSNodeJS.Checked    := cnf.ReadString('nodejs', 'gus', 'true') = 'true';
  cbNodeJSNodeJS.Checked := cnf.ReadString('nodejs', 'nodejs', 'true') = 'true';
  cbQISCUSNodeJS.Checked := cnf.ReadString('nodejs', 'qiscus', 'true') = 'true';
  cbAutoRun.Checked      := cnf.ReadString('nodejs', 'autorun', 'false') = 'true';
  cbAutoStart.Checked    := cnf.ReadString('nodejs', 'autostart', 'false') = 'true';

  cbModulusSystem.Checked := cnf.ReadString('modulus', 'aktif', 'false') = 'true';
  edFaktorModulus.Text    := cnf.ReadString('modulus', 'faktor', '2');
  edNilaiModulus.Text     := cnf.ReadString('modulus', 'nilai', '0, 1');

  edTeleToken.Text := cnf.ReadString('telegram', 'token', '');
end;

procedure TfrmUtama.btnTestKirimClick(Sender: TObject);
var
  stt: TSendMessageStatus;
begin
  stt := sendMessage(edNomorTestKirim.Text, memPesanTestKirim.Text, cbTypeTestKirim.Text);

  if stt.Success = stSuccess then
  begin
    ShowMessage('Berhasil Kirim pesan'#13#10'Response bisa dilihat di log.');
    writeLog(stt.Response, '0004');
  end else
  begin
    MessageDlg('Gagal kirim Pesan, untuk detail cek log', mtError, [mbok], 0);
  end;
end;

procedure TfrmUtama.btnStartTestKirimClick(Sender: TObject);
begin
  if url = '' then
  begin
    MessageDlg('URL NPM belum disetting...', mtError, [mbok], 0);
    exit;
  end;

  if timNodeJS.Enabled then
  begin
    timNodeJS.Enabled         := false;
    btnStartTestKirim.Caption := 'START';
    btnStartNodeJS.Caption    := 'START NODEJS';
    btnStartQRCode.Caption    := 'START';
    writeLog('Gateway stop...', '0005');
  end else
  begin
    timNodeJS.Enabled         := true;
    btnStartTestKirim.Caption := 'STOP';
    btnStartNodeJS.Caption    := 'STOP NODEJS';
    btnStartQRCode.Caption    := 'STOP';
    writeLog('Gateway start...', '0006');
  end;
end;

procedure TfrmUtama.btnBrowseWAAPIAppClick(Sender: TObject);
begin
  OPenFile.InitialDir := pth + 'node_mrlee';
  OpenFile.FileName   := 'appJM.js';

  if OpenFile.Execute then
  begin
    edWAAPIApp.Text := OpenFile.FileName;
    cnf.WriteString('general', 'npm_path', edWAAPIApp.Text);
    cnf.UpdateFile;
    dos := edWAAPIApp.Text;
  end;
end;

procedure TfrmUtama.btnTestKirimQISCUSClick(Sender: TObject);
var
  sqi: TSendQiscus;
begin
  sqi := SendQiscus(edAppQISCUS.Text, edNomorQISCUS.Text, trim(memPesanQISCUS.Text));
  ShowMessage(sqi.response);
end;

procedure TfrmUtama.btnRUNNodeJSClick(Sender: TObject);
begin
  writeLog('Nodejs running...', '0007');
  StatusBar1.Panels[1].Text := 'Nodejs running...';
  dosComm.CommandLine := format('"%s" "%s"', [nod, dos]);
  dosComm.Execute;
end;

procedure TfrmUtama.btnBrowseNodejsAppClick(Sender: TObject);
begin
begin
  OPenFile.InitialDir := pth + 'node_mrlee\nodejs';
  OpenFile.FileName   := 'node.exe';

  if OpenFile.Execute then
  begin
    edNodeJSApp.Text := OpenFile.FileName;
    cnf.WriteString('general', 'node_path', edNodeJSApp.Text);
    cnf.UpdateFile;
    nod := edNodeJSApp.Text;
  end;
end;
end;

procedure TfrmUtama.btnDDLInfoClick(Sender: TObject);
begin
  frmDDL.ShowModal;
end;

procedure TfrmUtama.btnGetPortClick(Sender: TObject);
var
  sss: string;
  lst: TStringList;
  pp1: Integer;
  pp2: Integer;
begin
  sss := pth + 'node_mrlee/appJM.js';

  //check apakah file ada;
  if not FileExists(sss) then
  begin
    MessageDlg('File appJM.js tidak ditemukan.', mtError, [mbOK], 0);
    exit;
  end;

  lst := TStringList.Create;
  lst.LoadFromFile(sss);

  //ambil baris ke 10;
  sss := lst[9];
  //check apakah ada keyword port;
  if pos('port', sss) = 0 then
  begin
    MessageDlg('Keyword "port" tidak ditemukan.', mtError, [mbOK], 0);
    exit;
  end;

  pp1 := pos('=', sss);
  pp2 := pos(';', sss);
  sss := trim(Copy(sss, pp1 + 1, pp2 - pp1 - 1));

  lst.Free;
  ShowMessage('Port ditemukan : ' + sss);
  edNPMURL.Text := 'http://localhost:' + sss;
end;

procedure TfrmUtama.btnConnectClick(Sender: TObject);
begin
  with conn.Params do
  begin
    Clear;
    Add('DriverID=MySQL');
    Add('Server='    + edHost.Text);
    Add('Port='      + edPort.Text);
    Add('User_Name=' + edUser.Text);
    Add('Password='  + edPass.Text);
    Add('Database='  + edDatabase.Text);
  end;
  conn.Connected := true;

  if conn.Connected then
  begin
    StatusBar1.Panels[2].Text := 'Database : connected';

    cnf.WriteString('database', 'host', edHost.Text);
    cnf.WriteString('database', 'port', edPort.Text);
    cnf.WriteString('database', 'username', edUser.Text);
    cnf.WriteString('database', 'password', edPass.Text);
    cnf.WriteString('database', 'database', edDatabase.Text);
    cnf.UpdateFile;

    ShowMessage('Database telah terhubung');
    memLog.Clear;
  end;
end;

procedure TfrmUtama.btnJumlahAntrianClick(Sender: TObject);
var
  sss: string;
  xxx: Integer;
begin
  sss := 'SELECT COUNT(*) jumlah_antrian FROM wa_outbox '
          + 'WHERE STATUS="ANTRIAN"';

  zqrGeneral.SQL.Text := sss;

  try
    zqrGeneral.Open;
  except
    on E:EDatabaseError do
    begin
      MessageDlg(E.Message, mtError, [mbOK], 0);
      exit;
    end;
  end;

  xxx := zqrGeneral.FieldByName('jumlah_antrian').AsInteger;
  ShowMessage('Jumlah Antrian : ' + xxx.ToString);
end;

procedure TfrmUtama.btnLastQueryClick(Sender: TObject);
begin
  frmLastQuery.ShowModal;
end;

procedure TfrmUtama.btnResetNodeJSClick(Sender: TObject);
var
  dir: string;
begin
  dir := pth + '.wwebjs_cache';
  if DirectoryExists(dir) then
  begin
    TDirectory.Delete(dir, True);
  end;

  dir := pth + '.wwebjs_auth';
  if DirectoryExists(dir) then
  begin
   TDirectory.Delete(dir, True);
  end;
  ShowMessage('Hapus session selesai...');
end;

procedure TfrmUtama.btnSetPortClick(Sender: TObject);
var
  fil: string;
  lst: TStrings;
  sss: string;
begin
  sss := pth + 'node_mrlee/appJM.js';
  fil := sss;

  //check apakah file ada;
  if not FileExists(sss) then
  begin
    MessageDlg('File appJM.js tidak ditemukan.', mtError, [mbOK], 0);
    exit;
  end;

  lst := TStringList.Create;
  lst.LoadFromFile(sss);

  //ambil baris ke 10;
  sss := lst[9];
  //check apakah ada keyword port;
  if pos('port', sss) = 0 then
  begin
    MessageDlg('Keyword "port" tidak ditemukan.', mtError, [mbOK], 0);
    exit;
  end;

  url_text := edNPMURL.Text;
  frmSetPort.ShowModal;

  if isset_port then
  begin
    sss := 'const port = ' + int_port.ToString + ';';
    edNPMURL.Text := 'http://localhost:' + int_port.ToString;
    lst[9] := sss;
    lst.SaveToFile(fil);
    ShowMessage('Berhasil setting port : ' + int_port.ToString);
  end;

  lst.Free;
end;

procedure TfrmUtama.btnTestKirimTeleClick(Sender: TObject);
var
  stt: TSendMessageStatus;
begin
  stt := SendTele(edTeleID.Text, edTextTele.Text);

  if stt.Success = stSuccess then
  begin
    ShowMessage('Pesan berhasil dikirim');
  end else
  begin
    MessageDlg('Kirim pesan gagal'#13#10'Silahkan lihat log..', mtError, [mbOK],
       0);
    writeLog(stt.Response, '0008');
  end;
end;

procedure TfrmUtama.cbAutaStartQiscusClick(Sender: TObject);
begin
  cnf.WriteString('qiscus', 'autostart', ifthen(cbAutaStartQiscus.Checked, 'true', 'false'));
  cnf.UpdateFile;
end;

procedure TfrmUtama.cbAutoRunClick(Sender: TObject);
begin
  cnf.WriteString('nodejs', 'autorun', ifthen(cbAutoRun.Checked, 'true', 'false'));
  cnf.UpdateFile;
end;

procedure TfrmUtama.cbAutoStartClick(Sender: TObject);
begin
  cnf.WriteString('nodejs', 'autostart', ifthen(cbAutoStart.Checked, 'true', 'false'));
  cnf.UpdateFile;
end;

procedure TfrmUtama.cbAutoSwitchClick(Sender: TObject);
begin
  cnf.WriteString('general', 'autoswitch', ifthen(cbAutoSwitch.Checked, 'true', 'false'));
  cnf.UpdateFile;
end;


procedure TfrmUtama.cbCUSNodeJSClick(Sender: TObject);
begin
  cnf.WriteString('nodejs', 'cus', ifthen(cbCUSNodeJS.Checked, 'true', 'false'));
  cnf.UpdateFile;
end;

procedure TfrmUtama.cbCUSQISCUSClick(Sender: TObject);
begin
  cnf.WriteString('qiscus', 'cus', ifthen(cbCUSQiscus.Checked, 'true', 'false'));
  cnf.UpdateFile;
end;

procedure TfrmUtama.cbDescMessageClick(Sender: TObject);
begin
  cnf.WriteString('general', 'descmessage', ifthen(cbDescMessage.Checked, 'true', 'false'));
  cnf.UpdateFile;
end;

procedure TfrmUtama.cbGUSNodeJSClick(Sender: TObject);
begin
  cnf.WriteString('nodejs', 'gus', ifthen(cbGUSNodeJS.Checked, 'true', 'false'));
  cnf.UpdateFile;
end;

procedure TfrmUtama.cbGUSQISCUSClick(Sender: TObject);
begin
  cnf.WriteString('qiscus', 'gus', ifthen(cbGUSQiscus.Checked, 'true', 'false'));
  cnf.UpdateFile;
end;

procedure TfrmUtama.cbModulusSystemClick(Sender: TObject);
begin
  cnf.WriteString('modulus', 'aktif', ifthen(cbModulusSystem.Checked, 'true', 'false'));
  cnf.UpdateFile;
end;

procedure TfrmUtama.cbNodeJSNodeJSClick(Sender: TObject);
begin
  cnf.WriteString('nodejs', 'nodejs', ifthen(cbNodeJSNodeJS.Checked, 'true', 'false'));
  cnf.UpdateFile;
end;

procedure TfrmUtama.cbNodeJSQiscusClick(Sender: TObject);
begin
  cnf.WriteString('qiscus', 'nodejs', ifthen(cbNodeJSQiscus.Checked, 'true', 'false'));
  cnf.UpdateFile;
end;

procedure TfrmUtama.cbQISCUSNodeJSClick(Sender: TObject);
begin
  cnf.WriteString('nodejs', 'qiscus', ifthen(cbQISCUSNodeJS.Checked, 'true', 'false'));
  cnf.UpdateFile;
end;

procedure TfrmUtama.cbQiscusQiscusClick(Sender: TObject);
begin
  cnf.WriteString('qiscus', 'qiscus', ifthen(cbQISCUSNodeJS.Checked, 'true', 'false'));
  cnf.UpdateFile;
end;

procedure TfrmUtama.cbStopNodeJSClick(Sender: TObject);
begin
  cnf.WriteString('general', 'stopnodejs', ifthen(cbStopNodeJS.Checked, 'true', 'false'));
  cnf.UpdateFile;
end;

procedure TfrmUtama.ConnectingDb;
begin
  edHost.Text     := cnf.ReadString('database', 'host', 'localhost');
  edPort.Text     := cnf.ReadString('database', 'port', '3306');
  edUser.Text     := cnf.ReadString('database', 'username', 'root');
  edPass.Text     := cnf.ReadString('database', 'password', '');
  edDatabase.Text := cnf.ReadString('database', 'database', 'wagateway');

  with conn.Params do
  begin
    Clear;
    Add('DriverID=MySQL');
    Add('Server='    + edHost.Text);
    Add('Port='      + edPort.Text);
    Add('User_Name=' + edUser.Text);
    Add('Password='  + edPass.Text);
    Add('Database='  + edDatabase.Text);
  end;
  conn.Connected := true;

  if conn.Connected then
  begin
    StatusBar1.Panels[2].Text := 'Database : connected';
  end;
end;

procedure TfrmUtama.dosCommNewLine(ASender: TObject; const ANewLine: string;
    AOutputType: TOutputType);
begin
  memDOSCOM.Lines.Add(ANewLine);

  //suruh scan qrcode;
  if pos('QR->  ', ANewLine) > 0 then
  begin
    showQRCode(copy(ANewLine, 7, Length(ANEwLine)), imgQRCode);
    memLog.Clear;
    PageControl1.ActivePage := tabQRCode;
    lblStatusQRCode.Caption := '';
  end;

  //jika belum jln;
  if pos('AUTHENTICATED', ANewLine) > 0 then
  begin
    auth := true;
    StatusBar1.Panels[1].Text := 'Nodejs : AUTHENTICATED';
    memLog.Clear;
  end;

  //jika sudah jln sebelum nya;
  if pos('EADDRINUSE: address already in use', ANewLine) > 0 then
  begin
    auth := true;
    StatusBar1.Panels[1].Text := 'Nodejs : AUTHENTICATED';
    memLog.Clear;
  end;

  if pos('WA Gate is ready!', ANewLine) > 0 then
  begin
    imgQRCode.Picture := nil;
    lblStatusQRCode.Caption := 'WA Gate is ready!';

    if cbAutoStart.Checked then
    begin
      timNodeJS.Enabled := false;
      btnStartTestKirimClick(nil);
    end;
  end;

  if pos('error', LowerCase(ANewLine)) > 0 then
  begin
    lblStatusQRCode.Caption := 'ERROR';

    if cbStopNodeJS.Checked then
    begin
      timNodeJS.Enabled := true;
      btnStartTestKirimClick(nil);
      writeLog('Stop NodeJS', '0041');
    end;

    if cbAutoSwitch.Checked then
    begin
      btnRunQISCUS.Caption := 'RUN';
      btnRunQISCUSClick(nil);
      writeLog('Start QISCUS', '0009');
    end;
  end;
end;

procedure TfrmUtama.edFaktorModulusChange(Sender: TObject);
begin
  cnf.WriteString('modulus', 'faktor', edFaktorModulus.Text);
  cnf.UpdateFile;
end;

procedure TfrmUtama.edIntervalNodeJSChange(Sender: TObject);
begin
  timNodeJS.Interval := StrToIntDef(edIntervalNodeJS.Text, 10) * 1000;
  cnf.WriteString('nodejs', 'interval', edIntervalNodeJS.Text);
  cnf.UpdateFile;
end;


procedure TfrmUtama.edIntervalQISCUSChange(Sender: TObject);
begin
  timQiscus.Interval := StrToIntDef(edIntervalQISCUS.Text, 3) * 1000;
  cnf.WriteString('qiscus', 'interval', edIntervalQISCUS.Text);
  cnf.UpdateFile;
end;

procedure TfrmUtama.edNilaiModulusChange(Sender: TObject);
begin
  cnf.WriteString('modulus', 'nilai', edNilaiModulus.Text);
  cnf.UpdateFile;
end;

procedure TfrmUtama.edNPMURLChange(Sender: TObject);
begin
  cnf.WriteString('general', 'npm_url', edNPMURL.Text);
  cnf.UpdateFile;
  url := edNPMURL.Text;
end;

procedure TfrmUtama.edParamQiscusChange(Sender: TObject);
begin
  cnf.WriteString('qiscus', 'param', edParamQiscus.Text);
  cnf.UpdateFile;
end;

procedure TfrmUtama.edQiscusIDChange(Sender: TObject);
begin
  cnf.WriteString('qiscus', 'id', edQiscusID.Text);
  cnf.UpdateFile;
end;

procedure TfrmUtama.edQiscusKeyChange(Sender: TObject);
begin
  cnf.WriteString('qiscus', 'key', edQiscusKey.Text);
  cnf.UpdateFile;
end;

procedure TfrmUtama.edTeleTokenChange(Sender: TObject);
begin
  cnf.WriteString('telegram', 'token', edTeleToken.Text);
  cnf.UpdateFile;
end;

procedure TfrmUtama.edTemplateQiscusChange(Sender: TObject);
begin
  cnf.WriteString('qiscus', 'template', edTemplateQiscus.Text);
  cnf.UpdateFile;
end;

function TfrmUtama.sendMessage(number: String; Message: String; tipe: String='japri'): TSendMessageStatus;
var
  lst: TStrings;
  stm: TStringStream;
  res: String;
  jso: TJSONObject;
  stt: String;
  tip: String;
  par: String;
begin
  if tipe = 'japri' then
  begin
    par := 'number';
    tip := 'send-message';
  end else
  begin
    par := 'id';
    tip := 'send-group';
  end;

  lst := TStringList.create;
  stm := TStringStream.Create;
  lst.Values[par]  := number;
  lst.Values['message'] := ParseUTF8(Message);

  result.Success  := stError;
  result.Response := '';
  result.Request  := StringReplace(lst.Text, '"', '\"', [rfReplaceAll]);

  try
    http.Post(url + '/' + tip, lst, stm);
    res := stm.DataString;
  except
    on E:Exception do
    begin
      writeLog(E.Message, '0010');
      result.Response := StringReplace(E.Message, '"', '\"', [rfReplaceAll]);
      result.Success  := stError;
      lst.Free;
      stm.Free;
      exit;
    end;
  end;
  lst.Free;
  stm.Free;

  jso := TJSONObject(TJSONObject.ParseJSONValue(res));

  try
    stt := jso.Values['status'].Value;
  except
    on E:Exception do
    begin
      writeLog(E.Message, '0011');
      result.Response := StringReplace(E.Message, '"', '\"', [rfReplaceAll]);
      result.Success  := stError;
      exit;
    end;
  end;

  if stt = 'true' then
  result.Success := stSuccess else
  result.Success := stFailed;

  if result.Success <> stSuccess then
  writeLog(res, '0012');

  result.Response := StringReplace(res, '"', '\"', [rfReplaceAll]);
end;

procedure TfrmUtama.timNodeJSTimer(Sender: TObject);
var
  des: string;
  fak: Integer;
  sss: String;
  nom: String;
  pes: String;
  idx: Integer;
  stt: TSendMessageStatus;
  fil: string;
  tip: string;
  med: String;
  typ: String;
  fi2: String;
  fi3: string;
  sen: string;
  xxx: Integer;
begin
  //Timer1.Enabled:= false;
  if LastDay <> Trunc(now) then
  begin
    memLog.Clear;
    writeLog('Log cleared...', '0013');
  end;
  LastDay := Trunc(now);

  isExec := not isExec;

  if isExec then
  begin
    //writeLog('Iddle State....');
    exit;
  end;

  if not auth then
  begin
    writeLog('NodeJS belum ter-AUTHENTICATED', '0014');
    exit;
  end;

  if not conn.Connected then
  begin
    writeLog('Database belum connected', '0015');
    conn.Connected := false;

    try
      conn.Connected := true;
    except
      on E:EDatabaseError do
      begin
        writeLog(E.Message, '0016');
      end;
    end;
    exit;
  end;

  //writeLog('Ambil Data....');
  fil := 'NOWA like "%@t.us"';
  if cbCUSNodeJS.Checked then
  begin
    fil := fil + 'or NOWA like "%@c.us"';
  end;

  if cbGUSNodeJS.Checked then
  begin
    fil := fil + ' or NOWA like "%@g.us"';
  end;

  fil := 'and (' + fil + ')';
  fi2 := '';

  if cbNodeJSNodeJS.Checked then
  fi2 := fi2 + ' or SENDER="NODEJS"';

  if cbQISCUSNodeJS.Checked then
  fi2 := fi2 + ' or SENDER="QISCUS"';

  fi3 := '';

  if cbModulusSystem.Checked then
  begin
    fak := StrToIntDef(edFaktorModulus.Text, 0);
    if fak < 2 then
    begin
      writeLog('Faktor modulus tidak valid (harus lebih besar dari 1)', '0017');
      exit;
    end;

    sss := edNilaiModulus.Text;
    if not ValidModulusValue(sss) then
    begin
      writeLog('Format nilai modulus tidak valid (contoh yang benar: 0, 1, 2), '
        + 'nilai bisa lebih dr satu dipisahkan dengan koma', '0018');
      exit;
    end;

    fi3 := format(' and MOD(NOMOR, %d) in (%s)', [fak, sss]);
  end;

  des := '';
  if cbDescMessage.Checked then
  des := ' order by TANGGAL_JAM desc ';

  sss := 'select NOMOR, NOWA, PESAN, FILE, TYPE, SENDER from wa_outbox where '
          + 'STATUS="ANTRIAN" and length(NOWA) > 12 ' + fil + ' and ('
          + 'SENDER = "ANY" or SENDER="TELEGRAM"' + fi2 + ') and '
          + 'TANGGAL_JAM <= now() ' + fi3 + des + ' limit 1';
  last_query := sss;
  zqr.SQL.Text := sss;
  try
    zqr.Open;
  except
    on E:EDatabaseError do
    begin
      writeLog(E.Message, '0019');
      exit;
    end;
  end;

  if zqr.IsEmpty then exit;

  try
    nom := zqr.fieldByName('NOWA').AsString;
    pes := zqr.FieldByName('PESAN').AsString;
    idx := zqr.FieldByName('NOMOR').AsInteger;
    med := zqr.FieldByName('FILE').AsString;
    typ := zqr.FieldByName('TYPE').AsString;
    sen := zqr.FieldByName('SENDER').AsString;
    pes := pes + #13#10#13#10'#' + format('%.6d', [idx]);
  except
    on E:Exception do
    begin
      writeLog(E.Message, '0040');
      exit;
    end;
  end;

  writeLog('Data Terambil ID : ' + idx.ToString, '0020');
  writeLog('Kirim Pesan ID : ' + idx.ToString, '0021');

  if sen = 'TELEGRAM' then
  begin
    try
      if typ = 'TEXT' then
      stt := sendTele(nom, pes);
    except
      on E:Exception do
      begin
        writeLog(E.Message, '0022');
        exit;
      end;
    end;
    stt.Response := StringReplace(stt.Response, '"', '\"', [rfReplaceAll]);
  end else
  begin
    tip := ifthen(pos('@c.us', nom) > 0, 'japri', 'group');
    try
      if typ = 'TEXT' then
      stt := sendMessage(nom, pes, tip) else
      stt := sendFile(nom, pes, med);
    except
      on E:Exception do
      begin
        writeLog(E.Message, '0022');
        exit;
      end;
    end;
  end;

  if stt.Success = stSuccess then
  writeLog('Kirim Pesan ID : ' + idx.ToString + ' berhasil', '0023') else
  writeLog('Kirim Pesan ID : ' + idx.ToString + ' gagal', '0024');

  if stt.Success <> stError then
  begin
    xxx := ifthen(stt.Success = stSuccess, 1, 0);

    writeLog('Update data ID : ' + idx.ToString, '0025');
    sss := format('update wa_outbox set STATUS="Terproses", SUCCESS="%d", '
             + 'RESPONSE="%s", REQUEST="%s" where NOMOR="%d"',
            [xxx, stt.Response, stt.Request, idx]);

    sss := StringReplace(sss, '\\"', '\"', [rfReplaceAll]);

    zqr.SQL.Text := sss;
    try
      zqr.ExecSQL;
      writeLog('Update data ID : ' + idx.ToString + ' berhasil', '0026');
    except
      on E:EDatabaseError do
      begin
        writeLog(E.Message, '0027');
        writeLog(sss, '0028');
        exit;
      end;
    end;
  end;
  //Timer1.Enabled := true;
end;

procedure TfrmUtama.timQiscusTimer(Sender: TObject);
var
  des: string;
  ext: TExtractMessage;
  idx: Integer;
  nom: string;
  pes: string;
  sou: string;
  sqi: TSendQiscus;
  ss1: string;
  sss: string;
  suc: Integer;
  xxx: Integer;
  fil: String;
  fi2: String;
begin
  if LastDay <> Trunc(now) then
  begin
    memLog.Clear;
    writeLog('Log cleared...', '0029');
  end;
  LastDay := Trunc(now);

  if not conn.Connected then
  begin
    writeLog('Database berlum connected', '0030');
    exit;
  end;

  //writeLog('Ambil Data....');
  fil := '';

  if cbNodeJSQiscus.Checked then
  fil := fil + ' or SENDER="NODEJS"';

  if cbQiscusQiscus.Checked then
  fil := fil + ' or SENDER="QISCUS"';

  fi2 := '';

  if cbCUSQISCUS.Checked then
  fi2 := fi2 + 'NOWA like "%c.us"';

  if cbGUSQISCUS.Checked then
  begin
    if fi2 <> '' then
    fi2 := fi2 + ' or ';
    fi2 := fi2 + 'NOWA like "%g.us"';
  end;

  if fi2 <> '' then
  fi2 := 'and (' + fi2 + ')';

  des := '';
  if cbDescMessage.Checked then
  des :=  'order by TANGGAL_JAM desc ';

  sss := 'select NOMOR, NOWA, PESAN, SOURCE from wa_outbox where STATUS='
          + '"ANTRIAN" and length(NOWA) > 12 '+ fi2 + ' and '
          + '(SENDER = "ANY"' + fil + ') and TANGGAL_JAM <= now() ' + des
          + 'limit 1';


  last_query := sss;
  zqrQiscus.SQL.Text := sss;
  try
    zqrQiscus.Open;
  except
    on E:EDatabaseError do
    begin
      writeLog(E.Message, '0031');
      exit;
    end;
  end;


  if zqrQiscus.IsEmpty then exit;

  nom := zqrQiscus.fieldByName('NOWA').AsString;
  pes := zqrQiscus.FieldByName('PESAN').AsString;
  idx := zqrQiscus.FieldByName('NOMOR').AsInteger;
  sou := zqrQiscus.FieldByName('SOURCE').AsString;
  pes := pes + #13#10#13#10'#' + format('%.6d', [idx]);

  writeLog('Data Terambil ID : ' + idx.ToString, '0032');
  writeLog('Kirim Pesan ID : ' + idx.ToString, '0033');
  ext := ExtractMessage(pes, sou);
  sqi := SendQiscus(Ext.App, nom, ext.msg);
  xxx := pos('"error', sqi.response);

  if xxx > 0 then
  begin
    writeLog('Gagal Kirim Pesan ID : ' + idx.ToString, '0034');
    suc := 0;
  end else
  begin
    writeLog('Berhasil Kirim Pesan ID : ' + idx.ToString, '0035');
    suc := 1;
  end;

  sss := StringReplace(sqi.response, '"', '\"', [rfReplaceAll]);
  ss1 := StringReplace(sqi.request, '"', '\"', [rfReplaceAll]);
  ss1 := StringReplace(ss1, '\\"', '\"', [rfReplaceAll]);

  //update status;
  sss := format('update wa_outbox set STATUS="Terproses", RESPONSE="%s",'
           + 'SUCCESS="%d", REQUEST="%s" where NOMOR="%d"',
         [sss, suc, ss1, idx]);
  zqrQiscus.SQL.Text := sss;
  try
    zqrQiscus.ExecSQL;
    writeLog('Berhasil Update Pesan ID : ' + idx.ToString, '0036');
  except
    on E:EDatabaseError do
    begin
      writeLog(E.Message, '0037');
      writeLog('Gagal Update Pesan ID : ' + idx.ToString, '0038');
      writeLog('', '');
      writeLog(sss, '0039');
      writeLog('', '');
      exit;
    end;
  end;

  //timQiscus.Enabled := false;
end;

function TfrmUtama.SendQiscus(App, Nowa, msg: String): TSendQiscus;
var
  res: IHTTPResponse;
  sss: string;
  stm: TStringStream;
  url: string;
  lst: TStrings;
  iii: Integer;
  xxx: Integer;
begin
  lst := TStringList.Create;
  xxx := StrToIntDef(edParamQiscus.Text, 6);

  sss := '';
  for iii := 1 to xxx do
  sss := sss + #13#10;
  msg := trim(msg) + sss;

  lst.Text := msg;

  msg := '';
  for iii := 0 to xxx - 1 do
  begin
    sss := lst[iii];

    if sss = '' then
    sss := ' ';
    sss := StringReplace(sss, '"', '\"', [rfReplaceAll]);

    if msg <> '' then
    msg := msg + ',';
    msg := msg +  '{"type":"text","text":"' + sss +'"}';
  end;

  sss := '{'
         +    '"to": "%s",'
         +    '"type": "template",'
         +    '"template": {'
         +      '"namespace": "broadcast_alert",'
         +      '"name": "' + edTemplateQiscus.Text + '",'
         +      '"language": {'
         +        '"policy": "deterministic",'
         +        '"code": "id"'
         +      '},'
         +      '"components": ['
         +         '{'
         +            '"type" : "header",'
         +            '"parameters": [{"type":"text","text":"%s"}]'
         +         '},'
         +         '{'
         +            '"type" : "body",'
         +            '"parameters": [%s]'
         +         '}'
         +      ']'
         +    '}'
         + '}';


  sss := format(sss, [NoWA, App, msg]);

  stm := TStringStream.Create(sss);
  url := 'https://multichannel.qiscus.com/whatsapp/v1/pyhhn-swuwhaz5jj3tgzq/3131/messages';

  httpQiscus.CustomHeaders['Qiscus-App-Id']     := edQiscusID.Text;
  httpQiscus.CustomHeaders['Qiscus-Secret-Key'] := edQiscusKey.Text;
  httpQiscus.CustomHeaders['Content-Type']      := 'application/json';
  result.request := sss;

  try
    res := httpQiscus.Post(url, stm);
    sss := res.ContentAsString;
  except
    on E:Exception do
    sss := E.Message;
  end;

  result.response := sss;
end;

function TfrmUtama.ExtractMessage(Msg, source: String): TExtractMessage;
var
  xxx: Integer;
begin
  //apakah char 1 adalah [;
  if msg[1] <> '[' then
  begin
    result.App := source;
    result.Msg := Msg;
    exit;
  end;

  //detek char penutup ];
  xxx := pos(']', msg);

  if xxx = 0 then
  begin
    result.App := source;
    result.Msg := Msg;
    exit;
  end;

  result.Msg := Copy(Msg, xxx + 1, Length(Msg));
  result.App := Copy(Msg, 2, xxx - 2);
end;

procedure TfrmUtama.FormShow(Sender: TObject);
begin
  if Tag = 0 then
  begin
    if cbAutoRun.Checked then
    btnRUNNodeJSClick(nil);

    if cbAutaStartQiscus.Checked then
    btnRunQISCUSClick(nil);
  end;

  Tag := 1;
end;

end.
