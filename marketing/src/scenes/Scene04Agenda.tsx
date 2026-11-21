import { AbsoluteFill, interpolate, useCurrentFrame } from "remotion";
import { AssetScreen } from "../components/AssetScreen";
import { CalendarIcon, ClockIcon } from "../components/Icons";
import { PhoneFrame, StatusBar } from "../components/PhoneFrame";
import { BottomNote, SceneHeading } from "../components/SceneHeading";
import { COLORS, FONT_FAMILY } from "../theme";

type AgendaItem = {
  time: string;
  lab: string;
  title: string;
  status: "Disetujui" | "Menunggu";
};

const PHASES: { date: string; day: string; filter: number; items: AgendaItem[] }[] = [
  {
    date: "18",
    day: "Rab",
    filter: 0,
    items: [
      {
        time: "08.00 – 10.00",
        lab: "Lab Komputer 1",
        title: "Praktikum Pemrograman",
        status: "Disetujui",
      },
      {
        time: "10.00 – 12.00",
        lab: "Lab Jaringan",
        title: "Responsi Jaringan Komputer",
        status: "Disetujui",
      },
      {
        time: "13.00 – 15.00",
        lab: "Lab Multimedia",
        title: "Praktikum Desain Konten",
        status: "Menunggu",
      },
    ],
  },
  {
    date: "18",
    day: "Rab",
    filter: 1,
    items: [
      {
        time: "09.00 – 11.00",
        lab: "Lab Multimedia",
        title: "Kelas Fotografi Digital",
        status: "Disetujui",
      },
      {
        time: "13.00 – 15.00",
        lab: "Lab Multimedia",
        title: "Praktikum Desain Konten",
        status: "Menunggu",
      },
      {
        time: "16.00 – 17.00",
        lab: "Lab Multimedia",
        title: "Kelas Editing Video",
        status: "Disetujui",
      },
    ],
  },
  {
    date: "19",
    day: "Kam",
    filter: 1,
    items: [
      {
        time: "08.00 – 10.00",
        lab: "Lab Multimedia",
        title: "Produksi Video Kreatif",
        status: "Disetujui",
      },
      {
        time: "10.00 – 12.00",
        lab: "Lab Multimedia",
        title: "Kelas Animasi 2D",
        status: "Disetujui",
      },
      {
        time: "15.00 – 17.00",
        lab: "Lab Multimedia",
        title: "Studio Audio & Podcast",
        status: "Disetujui",
      },
    ],
  },
];

const DAYS = [
  { day: "Sen", date: "16" },
  { day: "Sel", date: "17" },
  { day: "Rab", date: "18" },
  { day: "Kam", date: "19" },
];

export const Scene04Agenda: React.FC = () => {
  const frame = useCurrentFrame();
  const phase = frame < 30 ? 0 : frame < 60 ? 1 : 2;
  const phaseStart = [0, 30, 60][phase];
  const local = frame - phaseStart;
  const current = PHASES[phase];

  const enter = interpolate(local, [0, 8], [0, 1], {
    extrapolateLeft: "clamp",
    extrapolateRight: "clamp",
  });

  return (
    <AbsoluteFill
      style={{
        background:
          "radial-gradient(900px 650px at 0% 0%, #eef2ff 0%, rgba(244,246,251,0) 55%), #f4f6fb",
        fontFamily: FONT_FAMILY,
        overflow: "hidden",
      }}
    >
      <SceneHeading
        kicker="4d · Agenda"
        size={70}
        title="Agenda harian, filter lab"
      />

      <div
        style={{
          position: "absolute",
          top: 520,
          left: "50%",
          transform: "translateX(-50%)",
        }}
      >
        <PhoneFrame width={440} height={940}>
          <AssetScreen file="assets/agenda.png">
            <div
              style={{
                height: "100%",
                display: "flex",
                flexDirection: "column",
                background: COLORS.bg,
              }}
            >
              <StatusBar />

              <div
                style={{
                  padding: "16px 22px 18px",
                  background: "#ffffff",
                  borderBottom: `1px solid ${COLORS.line}`,
                }}
              >
                <div
                  style={{
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "space-between",
                    marginBottom: 14,
                  }}
                >
                  <div
                    style={{
                      fontSize: 25,
                      fontWeight: 800,
                      letterSpacing: -0.7,
                      color: COLORS.ink,
                    }}
                  >
                    Agenda Lab
                  </div>
                  <CalendarIcon size={24} color={COLORS.primary} />
                </div>

                <div style={{ display: "flex", gap: 7 }}>
                  {DAYS.map((item) => {
                    const active =
                      phase === 2 ? item.day === "Kam" : item.day === "Rab";
                    return (
                      <div
                        key={item.day}
                        style={{
                          flex: 1,
                          borderRadius: 12,
                          padding: "7px 0",
                          textAlign: "center",
                          background: active ? COLORS.primary : COLORS.surfaceSoft,
                          color: active ? "#fff" : COLORS.muted,
                          border: `1px solid ${active ? COLORS.primary : COLORS.line}`,
                        }}
                      >
                        <div style={{ fontSize: 11, fontWeight: 700 }}>
                          {item.day}
                        </div>
                        <div
                          style={{
                            fontSize: 17,
                            fontWeight: 800,
                            marginTop: 1,
                          }}
                        >
                          {item.date}
                        </div>
                      </div>
                    );
                  })}
                </div>

                <div style={{ display: "flex", gap: 7, marginTop: 12 }}>
                  {["Semua", "Multimedia", "Jaringan"].map((chip, index) => {
                    const active = index === current.filter;
                    return (
                      <span
                        key={chip}
                        style={{
                          fontSize: 12,
                          fontWeight: 700,
                          padding: "5px 10px",
                          borderRadius: 999,
                          background: active ? COLORS.primarySoft : "#fff",
                          color: active ? COLORS.primaryStrong : COLORS.muted,
                          border: `1px solid ${active ? COLORS.primary : COLORS.line}`,
                        }}
                      >
                        {chip}
                      </span>
                    );
                  })}
                </div>
              </div>

              <div
                style={{
                  flex: 1,
                  padding: "16px 20px",
                  display: "flex",
                  flexDirection: "column",
                  gap: 11,
                  opacity: enter,
                  transform: `translateY(${(1 - enter) * 16}px)`,
                }}
              >
                {current.items.map((item) => (
                  <div
                    key={`${phase}-${item.time}`}
                    style={{
                      background: "#ffffff",
                      borderRadius: 16,
                      border: `1px solid ${COLORS.line}`,
                      padding: "14px 16px",
                      display: "flex",
                      flexDirection: "column",
                      gap: 7,
                    }}
                  >
                    <div
                      style={{
                        display: "flex",
                        alignItems: "center",
                        justifyContent: "space-between",
                      }}
                    >
                      <span
                        style={{
                          fontSize: 14,
                          fontWeight: 800,
                          color: COLORS.primary,
                          background: COLORS.primarySoft,
                          padding: "4px 9px",
                          borderRadius: 7,
                          display: "flex",
                          alignItems: "center",
                          gap: 5,
                        }}
                      >
                        <ClockIcon size={13} color={COLORS.primary} />
                        {item.time}
                      </span>
                      <span
                        style={{
                          fontSize: 11,
                          fontWeight: 800,
                          color:
                            item.status === "Disetujui"
                              ? COLORS.success
                              : COLORS.warning,
                          background:
                            item.status === "Disetujui"
                              ? COLORS.successSoft
                              : COLORS.warningSoft,
                          padding: "4px 9px",
                          borderRadius: 999,
                        }}
                      >
                        {item.status}
                      </span>
                    </div>
                    <div
                      style={{
                        fontSize: 19,
                        fontWeight: 800,
                        color: COLORS.ink,
                        letterSpacing: -0.3,
                      }}
                    >
                      {item.title}
                    </div>
                    <div
                      style={{
                        fontSize: 13,
                        fontWeight: 600,
                        color: COLORS.muted,
                      }}
                    >
                      {item.lab}
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </AssetScreen>
        </PhoneFrame>
      </div>

      <BottomNote>Filter tanggal dan lab, jadwal langsung terlihat.</BottomNote>
    </AbsoluteFill>
  );
};
