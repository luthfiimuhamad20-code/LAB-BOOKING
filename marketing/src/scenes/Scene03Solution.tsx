import { AbsoluteFill, interpolate, spring, useCurrentFrame, useVideoConfig } from "remotion";
import { SparklesIcon } from "../components/Icons";
import { LogoMark } from "../components/Logo";
import { PhoneFrame, StatusBar } from "../components/PhoneFrame";
import { COLORS, FONT_FAMILY, GRADIENT_TEXT } from "../theme";

const LoginField: React.FC<{ label: string; value: string }> = ({
  label,
  value,
}) => (
  <div style={{ display: "flex", flexDirection: "column", gap: 7 }}>
    <div style={{ fontSize: 15, fontWeight: 700, color: COLORS.ink2 }}>
      {label}
    </div>
    <div
      style={{
        height: 46,
        borderRadius: 12,
        border: `1px solid ${COLORS.line}`,
        background: COLORS.surfaceSoft,
        display: "flex",
        alignItems: "center",
        padding: "0 14px",
        fontSize: 17,
        color: COLORS.muted,
      }}
    >
      <span style={{ filter: "blur(3.5px)", opacity: 0.75 }}>{value}</span>
    </div>
  </div>
);

export const Scene03Solution: React.FC = () => {
  const frame = useCurrentFrame();
  const { fps } = useVideoConfig();

  const mark = spring({
    frame: frame - 6,
    fps,
    config: { damping: 13, mass: 0.7, stiffness: 120 },
  });

  const heading = spring({
    frame: frame - 22,
    fps,
    config: { damping: 200, stiffness: 110 },
  });

  const phone = spring({
    frame: frame - 34,
    fps,
    config: { damping: 200, stiffness: 90 },
  });

  return (
    <AbsoluteFill
      style={{
        background:
          "radial-gradient(1000px 700px at 85% -5%, #e6ecff 0%, rgba(255,255,255,0) 60%), radial-gradient(800px 600px at -10% 100%, #e6fffb 0%, rgba(255,255,255,0) 60%), #ffffff",
        fontFamily: FONT_FAMILY,
        overflow: "hidden",
      }}
    >
      <div
        style={{
          position: "absolute",
          top: -220,
          right: -180,
          width: 720,
          height: 720,
          borderRadius: "50%",
          background:
            "linear-gradient(135deg, rgba(91,127,251,0.16), rgba(45,212,191,0.05))",
        }}
      />

      {[
        { top: 250, left: 170, size: 30, delay: 0 },
        { top: 420, right: 190, size: 24, delay: 10 },
        { top: 190, right: 330, size: 18, delay: 20 },
        { top: 470, left: 280, size: 20, delay: 28 },
      ].map((spark, i) => {
        const twinkle = 0.55 + Math.sin((frame + i * 14) * 0.14) * 0.35;
        return (
          <div
            key={i}
            style={{
              position: "absolute",
              top: spark.top,
              left: spark.left,
              right: spark.right,
              opacity: twinkle,
            }}
          >
            <SparklesIcon size={spark.size} color={COLORS.sky} />
          </div>
        );
      })}

      <div
        style={{
          position: "absolute",
          top: 250,
          left: 0,
          right: 0,
          display: "flex",
          justifyContent: "center",
          transform: `scale(${0.7 + mark * 0.3})`,
          opacity: mark,
        }}
      >
        <LogoMark size={236} radius={64} />
      </div>

      <div
        style={{
          position: "absolute",
          top: 540,
          left: 64,
          right: 140,
          textAlign: "center",
          opacity: heading,
          transform: `translateY(${(1 - heading) * 28}px)`,
        }}
      >
        <div
          style={{
            fontSize: 30,
            fontWeight: 700,
            letterSpacing: 5,
            color: COLORS.primary,
            marginBottom: 16,
          }}
        >
          KENALIN
        </div>
        <div
          style={{
            fontSize: 88,
            lineHeight: 1,
            fontWeight: 800,
            letterSpacing: -3,
            color: COLORS.ink,
          }}
        >
          LAB{" "}
          <span style={GRADIENT_TEXT}>BOOKING</span>
        </div>
        <div
          style={{
            marginTop: 26,
            fontSize: 30,
            lineHeight: 1.35,
            fontWeight: 500,
            color: COLORS.muted,
          }}
        >
          Satu tempat untuk jadwal
          <br />
          laboratorium PKK UPI.
        </div>
      </div>

      <div
        style={{
          position: "absolute",
          top: 880,
          left: "50%",
          transform: `translateX(-50%) translateY(${(1 - phone) * 60}px)`,
          opacity: phone,
        }}
      >
        <PhoneFrame width={340} height={660} glow>
          <div
            style={{
              height: "100%",
              display: "flex",
              flexDirection: "column",
              background: "#ffffff",
              paddingBottom: 20,
            }}
          >
            <StatusBar />
            <div
              style={{
                padding: "14px 24px 0",
                display: "flex",
                flexDirection: "column",
                gap: 16,
                flex: 1,
              }}
            >
              <div
                style={{
                  display: "flex",
                  alignItems: "center",
                  gap: 10,
                }}
              >
                <LogoMark size={44} radius={12} />
                <div
                  style={{
                    fontSize: 19,
                    fontWeight: 800,
                    letterSpacing: -0.4,
                    color: COLORS.ink,
                  }}
                >
                  LAB BOOKING
                </div>
              </div>

              <div>
                <div
                  style={{
                    fontSize: 26,
                    fontWeight: 800,
                    letterSpacing: -0.8,
                    color: COLORS.ink,
                  }}
                >
                  Masuk ke akun
                </div>
                <div style={{ fontSize: 15, color: COLORS.muted, marginTop: 4 }}>
                  Kelola jadwal lab dalam satu tempat.
                </div>
              </div>

              <LoginField label="Email" value="nama@email.ac.id" />
              <LoginField label="Password" value="••••••••••" />

              <div
                style={{
                  height: 48,
                  borderRadius: 12,
                  background: COLORS.primary,
                  color: "#fff",
                  display: "flex",
                  alignItems: "center",
                  justifyContent: "center",
                  fontSize: 17,
                  fontWeight: 800,
                  boxShadow: "0 12px 24px rgba(59,91,253,0.28)",
                }}
              >
                Masuk
              </div>

              <div
                style={{
                  marginTop: "auto",
                  textAlign: "center",
                  fontSize: 14,
                  color: COLORS.muted,
                }}
              >
                Belum punya akun?{" "}
                <span style={{ color: COLORS.primary, fontWeight: 700 }}>
                  Daftar
                </span>
              </div>
            </div>
          </div>
        </PhoneFrame>
      </div>
    </AbsoluteFill>
  );
};
