const fs = require("fs");
const {
  Document, Packer, Paragraph, TextRun, Table, TableRow, TableCell,
  WidthType, AlignmentType, BorderStyle, ShadingType, HeadingLevel,
} = require("docx");

const FONT = "Nirmala UI";

const p = (text, opts = {}) =>
  new Paragraph({
    spacing: { line: 312, before: 60, after: 60 },
    children: [new TextRun({ text, font: FONT, size: 21, ...(opts.run || {}) })],
    ...(opts.para || {}),
  });

const step = (num, text, highlight = false) =>
  new Paragraph({
    spacing: { line: 312, before: 60, after: 60 },
    children: [
      new TextRun({ text: `Step ${num}: `, font: FONT, size: 21, bold: true, color: highlight ? "C00000" : "1F4E79" }),
      new TextRun({ text, font: FONT, size: 21 }),
    ],
  });

const copyBox = (label, value) =>
  new Paragraph({
    spacing: { line: 312, before: 40, after: 100 },
    indent: { left: 500 },
    shading: { type: ShadingType.CLEAR, fill: "F2F2F2" },
    border: {
      left: { style: BorderStyle.SINGLE, size: 12, color: "1F4E79" },
      top: { style: BorderStyle.SINGLE, size: 2, color: "CCCCCC" },
      bottom: { style: BorderStyle.SINGLE, size: 2, color: "CCCCCC" },
      right: { style: BorderStyle.SINGLE, size: 2, color: "CCCCCC" },
    },
    children: [
      new TextRun({ text: `${label}:  `, font: FONT, size: 20, bold: true }),
      new TextRun({ text: value, font: "Consolas", size: 18, color: "222222" }),
    ],
  });

const h = (text, level = 1) =>
  new Paragraph({
    heading: level === 1 ? HeadingLevel.HEADING_1 : HeadingLevel.HEADING_2,
    spacing: { before: 240, after: 120 },
    children: [new TextRun({ text, font: FONT, bold: true, color: "1F4E79", size: level === 1 ? 28 : 23 })],
  });

const note = (text) =>
  new Paragraph({
    spacing: { line: 312, before: 80, after: 80 },
    indent: { left: 400 },
    shading: { type: ShadingType.CLEAR, fill: "FFF4E5" },
    border: { left: { style: BorderStyle.SINGLE, size: 12, color: "E8A33D" } },
    children: [new TextRun({ text: `\u26A0  ${text}`, font: FONT, size: 20, color: "7A5200" })],
  });

const bullet = (text) =>
  new Paragraph({
    spacing: { line: 312, before: 40, after: 40 },
    bullet: { level: 0 },
    children: [new TextRun({ text, font: FONT, size: 21 })],
  });

const children = [
  new Paragraph({
    alignment: AlignmentType.CENTER,
    spacing: { after: 60 },
    children: [new TextRun({ text: "Mailgun DNS Setup - Step by Step Guide", font: FONT, bold: true, size: 38, color: "1F4E79" })],
  }),
  new Paragraph({
    alignment: AlignmentType.CENTER,
    spacing: { after: 260 },
    border: { bottom: { style: BorderStyle.SINGLE, size: 8, color: "1F4E79" } },
    children: [new TextRun({ text: "Domain: yrc-bd.com  |  Total time: 10 minute  |  Kono technical jana lagbe na", font: FONT, size: 20, color: "555555" })],
  }),

  h("Part 1: cPanel e dhukun", 1),
  step(1, "Browser e khulun:  https://server1.yrc-bd.com:2083  (cPanel login page asbe)"),
  step(2, "Username ar Password diye login korun (hosting er main account, email account na)"),
  step(3, "Upar ekta search box ache - sekhane likhun:  zone editor"),
  step(4, "Result theke \"Zone Editor\" click korun"),
  step(5, "yrc-bd.com er shamne \"Manage\" button e click korun"),
  p("Ekhon apnar shamne DNS record der ekta list dekhabe. Ebong upor dan e \"+ Record\" button ache. Ebar amra 4ta record jog korbo - proti ta te niche deya field gulo thik evabe fill korun."),

  h("Part 2: Record 1 jog korun (TXT - SPF)", 1),
  step(1, "Upar \"+ Record\" button e click korun"),
  step(2, "Type dropdown theke \"TXT Record\" select korun"),
  step(3, "\"Name\" ghor e likhun:", true),
  copyBox("Name", "mg"),
  step(4, "\"Record\" ghor e paste korun:", true),
  copyBox("Record", "v=spf1 include:mailgun.org ~all"),
  step(5, "\"Save Record\" button e click korun. Record list e \"mg\" name e ekta notun TXT record dekhabe - That's it!"),

  h("Part 3: Record 2 jog korun (TXT - DKIM)", 1),
  p("Ei record ta ektu boro. Niche deya FULL value ta copy kore \"Record\" ghor e paste korun (docx theke select kore Ctrl+C, tarpor cPanel e Ctrl+V):", { run: { bold: true } }),
  step(1, "Abar \"+ Record\" > \"TXT Record\" select korun"),
  step(2, "\"Name\" ghor e likhun:", true),
  copyBox("Name", "mx._domainkey.mg"),
  step(3, "\"Record\" ghor e paste korun (puro ta, ek line e):", true),
  copyBox("Record", "k=rsa; p=MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDQEmw5FDaHmwhZoTxqaRbltVVvBZXOWO/TpqjOdTddKjBz3MLV5MyYkQEieK8MSylWF9eW2BigCzo5M62N8JlxZMUhb9CRd7e0R2yLGoQFtweNWtdRVU9TGVIFgExOdOGy/uJ1upEYzjyM28IQQoMRtH1EM9Ua3YulDKBZkZiwhwIDAQAB"),
  note("Key ta kub lomba - paste korar por dekhen ek line-i ache to? Majhkane line vanga ba space thakle abar copy korun."),
  step(4, "\"Save Record\" click korun"),

  h("Part 4: Record 3 jog korun (CNAME)", 1),
  step(1, "\"+ Record\" > ei bar \"CNAME Record\" select korun"),
  step(2, "\"Name\" ghor e likhun:", true),
  copyBox("Name", "email.mg"),
  step(3, "\"Points to\" / \"Record\" ghor e likhun:", true),
  copyBox("Points to", "mailgun.org"),
  step(4, "\"Save Record\" click korun"),

  h("Part 5: Record 4 jog korun (TXT - DMARC)", 1),
  step(1, "\"+ Record\" > \"TXT Record\" select korun"),
  step(2, "\"Name\" ghor e likhun:", true),
  copyBox("Name", "_dmarc.mg"),
  step(3, "\"Record\" ghor e paste korun (puro ta, ek line e):", true),
  copyBox("Record", "v=DMARC1; p=none; pct=100; fo=1; ri=3600; rua=mailto:e4e10bb@dmarc.mailgun.org,mailto:x6hw2ndfcou@inbox.ondmarc.com; ruf=mailto:e4e10bb@dmarc.mailgun.org,mailto:x6hw2ndfcou@inbox.ondmarc.com;"),
  step(4, "\"Save Record\" click korun"),

  note("Mailgun er DNS page e ar 2ta MX record (mxa.mailgun.org, mxb.mailgun.org) dekhabe - OIGULO ADD KORBEN NA. Oigulo shudhu mail received korar jonno. Amra shudhu pathabo, tai upore 4ta record-i enough."),

  h("Part 6: Sesh step - Mailgun e verify", 1),
  step(1, "Mailgun (app.mailgun.com) e login korun"),
  step(2, "Sending > Domains > mg.yrc-bd.com te jan"),
  step(3, "Dan upore \"Check status\" button e click korun"),
  step(4, "DNS update hote 5-30 minute lagte pare. Protita record er shamne \"Verified\" (shubuj tick) dekhale kaj complete!"),
  step(5, "Verified howar por Daief ke janun - se email config final kore test pathabe"),

  h("Kono kichu bhul hole?", 1),
  bullet("Record add korte giye bhul kore puronо record delete/edit korben na - shudhu notun gulo add korun"),
  bullet("Name likhar somoy \".yrc-bd.com\" nijei likhar dorkar nei - cPanel nije jure ney"),
  bullet("1 ghonta por o \"Unverified\" thakle screen shot niye Daief ke pathan"),
];

const doc = new Document({
  sections: [{
    properties: {
      page: { margin: { top: 1000, bottom: 1000, left: 1100, right: 1100 } },
    },
    children,
  }],
});

Packer.toBuffer(doc).then((buf) => {
  const targets = [
    "D:\\Aci Project\\yc_content_planning\\Mailgun-DNS-Setup-Easy-Guide.docx",
    "D:\\Aci Project\\yc_content_planning\\Mailgun-DNS-Setup-Easy-Guide-Final.docx",
  ];
  for (const out of targets) {
    try {
      fs.writeFileSync(out, buf);
      console.log("WROTE " + out + " (" + buf.length + " bytes)");
      return;
    } catch (e) {
      console.log("locked: " + out);
    }
  }
  console.log("ALL NAMES LOCKED - close Word and rerun");
});
