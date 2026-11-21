import { AbsoluteFill, useCurrentFrame, useVideoConfig } from "remotion";
import { ClockIcon, ListIcon, ShieldCheckIcon } from "../components/Icons";
import { BottomNote, Highlight, SceneHeading } from "../components/SceneHeading";
import { popIn } from "../lib/anim";
import { COLORS, FONT_FAMILY, SHADOW } from "../theme";

const BENEFITS = [
  {
    title: "Hemat waktu",
    description: "Booking mandiri tanpa chat bolak-balik.",
    icon: ClockIcon,
    color: COLORS.primary,
    soft: COLORS.primarySoft,
    delay: 18,
  },
  {
    title: "Transparan",
    description: "Status dan catatan admin selalu jelas.",
    icon: ListIcon,
    color: "#0874b5",
    soft: "#e9f5fd",
    delay: 46,
  },
  {
    title: "Anti bentrok",
    description: "Sistem cek slot otomatis sebelum diajukan.",
    icon: ShieldCheckIcon,
    color: COLORS.success,
    soft: COLORS.successSoft,
    delay: 74,
  },
];

export const Scene05Benefits: React.FC = () => {
  const frame = useCurrentFrame();
  const { fps } = useVideoConfig();

  return (
    <AbsoluteFill
      style={{
        background:
          "radial-gradient(900px 650px at 100% 0%, #e9fff8 0%, rgba(244,246,251,0) 55%), radial-gradient(800px 600px at 0% 100%, #eef2ff 0%, rgba(244,246,251,0) 55%), #f4f6fb",
        fontFamily: FONT_FAMILY,
        overflow: "hidden",
      }}
    >
      <div
        style={{
          position: "absolute",
          top: -180,
          right: -200,
          width: 640,
          height: 640,
          borderRadius: "50%",
          border: "2px solid rgba(45,212,191,0.16)",
        }}
      />

      <SceneHeading
        kicker="Manfaat"
        size={70}
        title={
          <>
            Hemat waktu.
            <br />
            <Highlight>Transparan.</Highlight> Anti bentrok.
          </>
        }
      />

      <div
        style={{
          position: "absolute",
          top: 660,
          left: 64,
          right: 140,
          display: "flex",
          flexDirection: "column",
          gap: 24,
        }}
      >
        {BENEFITS.map((benefit, index) => {
          const Icon = benefit.icon;
          const anim = popIn(frame, fps, benefit.delay, 15);

          return (
            <div
              key={benefit.title}
              style={{
                display: "flex",
                alignItems: "center",
                gap: 26,
                background: "#ffffff",
                borderRadius: 26,
                padding: "26px 30px",
                boxShadow: SHADOW.card,
                border: `1px solid ${COLORS.line}`,
                ...anim,
              }}
            >
              <div
                style={{
                  width: 104,
                  height: 104,
                  borderRadius: 26,
                  background: benefit.soft,
                  color: benefit.color,
                  display: "flex",
                  alignItems: "center",
                  justifyContent: "center",
                  flex: "none",
                }}
              >
                <Icon size={50} color={benefit.color} strokeWidth={2.1} />
              </div>

              <div style={{ flex: 1 }}>
                <div
                  style={{
                    fontSize: 40,
                    fontWeight: 800,
                    letterSpacing: -1,
                    color: COLORS.ink,
                  }}
                >
                  {benefit.title}
                </div>
                <div
                  style={{
                    marginTop: 6,
                    fontSize: 24,
                    lineHeight: 1.35,
                    fontWeight: 500,
                    color: COLORS.muted,
                  }}
                >
                  {benefit.description}
                </div>
              </div>

              <div
                style={{
                  fontSize: 22,
                  fontWeight: 800,
                  color: COLORS.line,
                  letterSpacing: 1,
                }}
              >
                0{index + 1}
              </div>
            </div>
          );
        })}
      </div>

      <BottomNote>Semua dalam satu aplikasi.</BottomNote>
    </AbsoluteFill>
  );
};
