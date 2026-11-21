import { AbsoluteFill, interpolate, useCurrentFrame } from "remotion";
import { AssetScreen } from "../components/AssetScreen";
import { MapPinIcon, SearchIcon, UsersIcon } from "../components/Icons";
import { PhoneFrame, StatusBar } from "../components/PhoneFrame";
import { BottomNote, SceneHeading } from "../components/SceneHeading";
import { COLORS, FONT_FAMILY } from "../theme";

type Lab = {
  code: string;
  name: string;
  location: string;
  capacity: number;
  facilities: string[];
  active: boolean;
};

const LABS: Lab[] = [
  {
    code: "LK-01",
    name: "Lab Komputer 1",
    location: "Gedung A, Lt. 2",
    capacity: 40,
    facilities: ["40 PC", "Proyektor", "AC"],
    active: true,
  },
  {
    code: "LM-01",
    name: "Lab Multimedia",
    location: "Gedung B, Lt. 1",
    capacity: 30,
    facilities: ["iMac", "Pen Tablet", "Studio"],
    active: true,
  },
  {
    code: "LJ-01",
    name: "Lab Jaringan",
    location: "Gedung A, Lt. 3",
    capacity: 24,
    facilities: ["Cisco", "Rack Server", "AC"],
    active: true,
  },
  {
    code: "LR-01",
    name: "Lab Robotika",
    location: "Gedung C, Lt. 1",
    capacity: 20,
    facilities: ["Kit Robotika", "3D Printer"],
    active: false,
  },
];

const LabCard: React.FC<{ lab: Lab; highlighted: boolean }> = ({
  lab,
  highlighted,
}) => (
  <div
    style={{
      background: "#ffffff",
      borderRadius: 20,
      padding: "18px 20px",
      border: `2px solid ${highlighted ? COLORS.primary : COLORS.line}`,
      boxShadow: highlighted
        ? "0 16px 36px rgba(59,91,253,0.18)"
        : "0 4px 14px rgba(16,24,40,0.04)",
      transform: `scale(${highlighted ? 1.015 : 1})`,
      display: "flex",
      flexDirection: "column",
      gap: 10,
    }}
  >
    <div style={{ display: "flex", alignItems: "center", gap: 10 }}>
      <span
        style={{
          fontSize: 13,
          fontWeight: 800,
          letterSpacing: 0.6,
          color: COLORS.primary,
          background: COLORS.primarySoft,
          padding: "4px 9px",
          borderRadius: 7,
        }}
      >
        {lab.code}
      </span>
      <span
        style={{
          marginLeft: "auto",
          fontSize: 13,
          fontWeight: 700,
          color: lab.active ? COLORS.success : COLORS.muted,
          background: lab.active ? COLORS.successSoft : "#f1f3f7",
          padding: "4px 10px",
          borderRadius: 999,
        }}
      >
        {lab.active ? "Aktif" : "Nonaktif"}
      </span>
    </div>

    <div
      style={{
        fontSize: 23,
        fontWeight: 800,
        letterSpacing: -0.4,
        color: COLORS.ink,
      }}
    >
      {lab.name}
    </div>

    <div style={{ display: "flex", alignItems: "center", gap: 14 }}>
      <span
        style={{
          display: "flex",
          alignItems: "center",
          gap: 5,
          fontSize: 15,
          fontWeight: 600,
          color: COLORS.muted,
        }}
      >
        <MapPinIcon size={16} color={COLORS.muted} />
        {lab.location}
      </span>
      <span
        style={{
          display: "flex",
          alignItems: "center",
          gap: 5,
          fontSize: 15,
          fontWeight: 600,
          color: COLORS.muted,
        }}
      >
        <UsersIcon size={16} color={COLORS.muted} />
        {lab.capacity} orang
      </span>
    </div>

    <div style={{ display: "flex", flexWrap: "wrap", gap: 6 }}>
      {lab.facilities.map((facility) => (
        <span
          key={facility}
          style={{
            fontSize: 13,
            fontWeight: 600,
            color: COLORS.ink2,
            background: COLORS.surfaceSoft,
            border: `1px solid ${COLORS.line}`,
            padding: "4px 9px",
            borderRadius: 6,
          }}
        >
          {facility}
        </span>
      ))}
    </div>
  </div>
);

export const Scene04Katalog: React.FC = () => {
  const frame = useCurrentFrame();
  const scroll = interpolate(frame, [12, 112], [0, -250], {
    extrapolateLeft: "clamp",
    extrapolateRight: "clamp",
  });
  const highlight = frame > 42 && frame < 100;

  return (
    <AbsoluteFill
      style={{
        background:
          "radial-gradient(900px 650px at 100% 0%, #e8edff 0%, rgba(244,246,251,0) 55%), #f4f6fb",
        fontFamily: FONT_FAMILY,
        overflow: "hidden",
      }}
    >
      <SceneHeading
        kicker="4a · Katalog"
        size={70}
        title="Katalog laboratorium"
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
          <AssetScreen file="assets/katalog.png">
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
                  padding: "18px 24px 16px",
                  background: "#ffffff",
                  borderBottom: `1px solid ${COLORS.line}`,
                }}
              >
                <div
                  style={{
                    fontSize: 27,
                    fontWeight: 800,
                    letterSpacing: -0.8,
                    color: COLORS.ink,
                  }}
                >
                  Temukan Lab
                </div>
                <div
                  style={{
                    marginTop: 12,
                    height: 46,
                    borderRadius: 14,
                    background: COLORS.surfaceSoft,
                    border: `1px solid ${COLORS.line}`,
                    display: "flex",
                    alignItems: "center",
                    gap: 10,
                    padding: "0 14px",
                    color: COLORS.muted2,
                    fontSize: 16,
                  }}
                >
                  <SearchIcon size={18} color={COLORS.muted2} />
                  Cari lab atau fasilitas…
                </div>
                <div
                  style={{
                    display: "flex",
                    gap: 8,
                    marginTop: 12,
                  }}
                >
                  {["Semua", "Komputer", "Multimedia"].map((chip, index) => (
                    <span
                      key={chip}
                      style={{
                        fontSize: 14,
                        fontWeight: 700,
                        padding: "6px 12px",
                        borderRadius: 999,
                        background:
                          index === 0 ? COLORS.primary : COLORS.surfaceSoft,
                        color: index === 0 ? "#fff" : COLORS.muted,
                        border:
                          index === 0
                            ? "1px solid transparent"
                            : `1px solid ${COLORS.line}`,
                      }}
                    >
                      {chip}
                    </span>
                  ))}
                </div>
              </div>

              <div
                style={{
                  flex: 1,
                  overflow: "hidden",
                  padding: "16px 20px",
                }}
              >
                <div
                  style={{
                    display: "flex",
                    flexDirection: "column",
                    gap: 14,
                    transform: `translateY(${scroll}px)`,
                  }}
                >
                  {LABS.map((lab, index) => (
                    <LabCard
                      key={lab.code}
                      lab={lab}
                      highlighted={index === 1 && highlight}
                    />
                  ))}
                </div>
              </div>
            </div>
          </AssetScreen>
        </PhoneFrame>
      </div>

      <BottomNote>Lokasi, kapasitas, dan fasilitas lengkap.</BottomNote>
    </AbsoluteFill>
  );
};
