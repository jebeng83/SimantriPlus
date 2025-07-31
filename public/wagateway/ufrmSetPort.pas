unit ufrmSetPort;

interface

uses
  Winapi.Windows, Winapi.Messages, System.SysUtils, System.Variants, System.Classes, Vcl.Graphics,
  Vcl.Controls, Vcl.Forms, Vcl.Dialogs, Vcl.StdCtrls;

type
  TfrmSetPort = class(TForm)
    Label1: TLabel;
    edPort: TEdit;
    btnSetPort: TButton;
    procedure btnSetPortClick(Sender: TObject);
    procedure FormShow(Sender: TObject);
  private
    { Private declarations }
  public
  end;

var
  frmSetPort: TfrmSetPort;
  isset_port: boolean;
  url_text: String;
  int_port: Integer;

implementation

{$R *.dfm}

procedure TfrmSetPort.btnSetPortClick(Sender: TObject);
begin
  isset_port := true;
  int_port   := StrToIntDef(edPort.Text, 8100);
  Close;
end;

procedure TfrmSetPort.FormShow(Sender: TObject);
var
  ppp: Integer;
  sss: string;
begin
  isset_port := false;
  ppp := pos(':', url_text, 7);
  sss := Copy(url_text, ppp + 1, 100);
  edPort.Text := sss;
end;

end.
