from pathlib import Path

from reportlab.lib import colors
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import mm
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfbase.ttfonts import TTFont
from reportlab.platypus import Paragraph, SimpleDocTemplate, Spacer, Table, TableStyle


ROOT = Path(__file__).resolve().parents[1]
OUTPUT = ROOT / "output" / "pdf" / "ralmaster-company-card.pdf"


def register_fonts() -> tuple[str, str]:
    regular = Path("C:/Windows/Fonts/arial.ttf")
    bold = Path("C:/Windows/Fonts/arialbd.ttf")
    if not regular.is_file() or not bold.is_file():
        raise FileNotFoundError("Arial fonts are required to generate the company card PDF")
    pdfmetrics.registerFont(TTFont("RalmasterArial", str(regular)))
    pdfmetrics.registerFont(TTFont("RalmasterArialBold", str(bold)))
    return "RalmasterArial", "RalmasterArialBold"


def build_pdf() -> None:
    regular, bold = register_fonts()
    OUTPUT.parent.mkdir(parents=True, exist_ok=True)

    document = SimpleDocTemplate(
        str(OUTPUT),
        pagesize=A4,
        rightMargin=22 * mm,
        leftMargin=22 * mm,
        topMargin=20 * mm,
        bottomMargin=20 * mm,
        title="Карточка компании РАЛМАСТЕР",
        author="ООО РАЛМАСТЕР",
        subject="Реквизиты компании",
    )

    styles = getSampleStyleSheet()
    title = ParagraphStyle(
        "TitleRalmaster",
        parent=styles["Title"],
        fontName=bold,
        fontSize=25,
        leading=30,
        textColor=colors.HexColor("#111E35"),
        spaceAfter=6 * mm,
    )
    lead = ParagraphStyle(
        "LeadRalmaster",
        parent=styles["BodyText"],
        fontName=regular,
        fontSize=10.5,
        leading=15,
        textColor=colors.HexColor("#526073"),
        spaceAfter=8 * mm,
    )
    section = ParagraphStyle(
        "SectionRalmaster",
        parent=styles["Heading2"],
        fontName=bold,
        fontSize=15,
        leading=20,
        textColor=colors.HexColor("#111E35"),
        spaceBefore=4 * mm,
        spaceAfter=3 * mm,
    )
    cell_label = ParagraphStyle(
        "CellLabelRalmaster",
        parent=styles["BodyText"],
        fontName=regular,
        fontSize=9.5,
        leading=13,
        textColor=colors.HexColor("#526073"),
    )
    cell_value = ParagraphStyle(
        "CellValueRalmaster",
        parent=styles["BodyText"],
        fontName=regular,
        fontSize=9.5,
        leading=13,
        textColor=colors.HexColor("#111E35"),
    )

    company = [
        ("Полное наименование", "Общество с ограниченной ответственностью «РАЛМАСТЕР»"),
        ("ИНН", "5003168900"),
        ("КПП", "500301001"),
        ("ОГРН", "1255000036125"),
        ("Юридический адрес", "142700, Московская область, г.о. Ленинский, г. Видное, Белокаменное шоссе, д. 20В, помещ. 2/3"),
    ]
    bank = [
        ("Банк", "АО «АЛЬФА-БАНК»"),
        ("БИК", "044525593"),
        ("Расчётный счёт", "40702810801240000909"),
        ("Корреспондентский счёт", "30101810200000000593"),
    ]
    contacts = [
        ("Телефон", "+7 (991) 947-20-00"),
        ("Электронная почта", "ralmaster@inbox.ru"),
        ("Производство", "Московская область, г. Видное, Белокаменное шоссе, д. 20В, помещ. 2/3"),
        ("Офис продаж", "г. Подольск, Комсомольская ул., д. 59, офис 328"),
    ]

    def data_table(rows: list[tuple[str, str]]) -> Table:
        values = [
            [Paragraph(label, cell_label), Paragraph(value, cell_value)]
            for label, value in rows
        ]
        table = Table(values, colWidths=[47 * mm, 105 * mm], hAlign="LEFT")
        table.setStyle(
            TableStyle(
                [
                    ("VALIGN", (0, 0), (-1, -1), "TOP"),
                    ("LEFTPADDING", (0, 0), (-1, -1), 0),
                    ("RIGHTPADDING", (0, 0), (-1, -1), 3 * mm),
                    ("TOPPADDING", (0, 0), (-1, -1), 3.2 * mm),
                    ("BOTTOMPADDING", (0, 0), (-1, -1), 3.2 * mm),
                    ("LINEBELOW", (0, 0), (-1, -2), 0.4, colors.HexColor("#D6DEE8")),
                    ("BACKGROUND", (0, 0), (-1, -1), colors.white),
                ]
            )
        )
        return table

    story = [
        Paragraph("РАЛМАСТЕР", title),
        Paragraph(
            "Карточка компании с актуальными юридическими, банковскими и контактными реквизитами.",
            lead,
        ),
        Paragraph("Реквизиты компании", section),
        data_table(company),
        Spacer(1, 3 * mm),
        Paragraph("Банковские реквизиты", section),
        data_table(bank),
        Spacer(1, 3 * mm),
        Paragraph("Контакты", section),
        data_table(contacts),
    ]

    document.build(story)


if __name__ == "__main__":
    build_pdf()
