import {
  AbsoluteFill,
  Img,
  interpolate,
  spring,
  staticFile,
  useCurrentFrame,
  useVideoConfig,
} from "remotion";
import { ArrowRightIcon, SparklesIcon } from "../components/Icons";
import { LogoMark } from "../components/Logo";
import { staticFileExists } from "../lib/static-files";
import { APP_URL } from "../script";
import { COLORS, FONT_FAMILY, GRADIENT, GRADIENT_TEXT } from "../theme";

const DOTS = [
  { x: 120, y: 260, size: 12, color: "#38bdf8" },
  { x: 900, y: 330, size: 9, color: "#2dd4bf" },
  { x: 170, y: 760, size: 8, color: "#5b7ffb" },
  { x: 940, y: 820, size: 13, color: "#38bdf8" },
  { x: 240, y: 1240, size: 10, color: "#2dd4bf" },
  { x: 850, y: 1300, size: 7, color: "#ffffff" },
  { x: 520, y: 200, size: 7, color: "#ffffff" },
  { x: 760, y: 1120, size: 8, color: "#5b7ffb" },
];

const QR_FILE = "assets/qr.png";
const QR_SIZE = 300;

const QrPlaceholder: React.FC = () => (
  <div
    style={{
      width: QR_SIZE,
      height: QR_SIZE,
      borderRadius: 20,
      background: COLORS.surfaceSoft,
      border: `2px dashed ${COLORS.line}`,
      display: "flex",
      flexDirection: "column",
      alignItems: "center",
      justifyContent: "center",
      gap: 16,
      color: COLORS.navy,
    }}
  >
    <svg
      width={148}
      height={148}
      viewBox="0 0 24 24"
      fill="none"
      stroke={COLORS.navy}
      strokeWidth="1.5"
    >
      <rect x="3" y="3" width="7" height="7" rx="1.5" />
      <rect x="5" y="5" width="3" height="3" fill={COLORS.navy} stroke="none" />
      <rect x="14" y="3" width="7" height="7" rx="1.5" />
      <rect x="16" y="5" width="3" height="3" fill={COLORS.navy} stroke="none" />
      <rect x="3" y="14" width="7" height="7" rx="1.5" />
      <rect x="5" y="16" width="3" height="3" fill={COLORS.navy} stroke="none" />
      <path d="M14 14h3v3h-3zM19 14h2M14 19h2M18 18h3v3h-3z" />
    </svg>
    <div style={{ fontSize: 20, fontWeight: 700, color: COLORS.muted }}>
      QR Anda
    </div>
  </div>
);

const QrCard: React.FC<{ progress: number }> = ({ progress }) => {
  const hasQr = staticFileExists(QR_FILE);

  return (
    <div
      style={{
        position: "absolute",
        top: 1005,
        left: 0,
        right: 0,
        display: "flex",
        justifyContent: "center",
        opacity: progress,
        transform: `translateY(${(1 - progress) * 28}px)`,
      }}
    >
      <div
        style={{
          width: 388,
          padding: 24,
          borderRadius: 40,
          background: "#ffffff",
          display: "flex",
          flexDirection: "column",
          alignItems: "center",
          gap: 16,
          boxShadow: "0 30px 80px rgba(0, 0, 0, 0.35)",
        }}
      >
        {hasQr ? (
          <Img
            src={staticFile(QR_FILE)}
            style={{
              width: QR_SIZE,
              height: QR_SIZE,
              objectFit: "contain",
              display: "block",
            }}
          />
        ) : (
          <QrPlaceholder />
        )}
        <div
          style={{
            fontSize: 26,
            fontWeight: 800,
            letterSpacing: -0.5,
            color: COLORS.ink,
          }}
        >
          Scan untuk booking
        </div>
      </div>
    </div>
  );
};

export const Scene06CTA: React.FC = () => {
  const frame = useCurrentFrame();
  const { fps } = useVideoConfig();

  const logo = spring({
    frame,
    fps,
    config: { damping: 13, mass: 0.8, stiffness: 110 },
  });

  const text = spring({
    frame: frame - 14,
    fps,
    config: { damping: 200, stiffness: 110 },
  });

  const button = spring({
    frame: frame - 32,
    fps,
    config: { damping: 14, mass: 0.7, stiffness: 130 },
  });

  const qr = spring({
    frame: frame - 58,
    fps,
    config: { damping: 16, mass: 0.8, stiffness: 120 },
  });

  const url = spring({
    frame: frame - 78,
    fps,
    config: { damping: 200, stiffness: 110 },
  });

  const wave = 1 + Math.sin(Math.max(0, frame - 70) * 0.09) * 0.012;

  return (
    <AbsoluteFill
      style={{
        background:
          "radial-gradient(1000px 800px at 85% -10%, #1d2f6b 0%, rgba(11,21,51,0) 62%), radial-gradient(900px 700px at 0% 110%, #0e3f49 0%, rgba(11,21,51,0) 62%), #0b1533",
        fontFamily: FONT_FAMILY,
        overflow: "hidden",
      }}
    >
      <div
        style={{
          position: "absolute",
          top: 110,
          left: "50%",
          transform: "translateX(-50%)",
          width: 1000,
          height: 1000,
          borderRadius: "50%",
          border: "2px solid rgba(56,189,248,0.10)",
        }}
      />
      <div
        style={{
          position: "absolute",
          top: 230,
          left: "50%",
          transform: "translateX(-50%)",
          width: 760,
          height: 760,
          borderRadius: "50%",
          border: "2px solid rgba(45,212,191,0.12)",
        }}
      />

      {DOTS.map((dot, index) => {
        const twinkle = 0.35 + Math.sin((frame + index * 20) * 0.1) * 0.3;
        return (
          <span
            key={index}
            style={{
              position: "absolute",
              top: dot.y,
              left: dot.x,
              width: dot.size,
              height: dot.size,
              borderRadius: "50%",
              background: dot.color,
              opacity: twinkle,
            }}
          />
        );
      })}

      <div
        style={{
          position: "absolute",
          top: 300,
          left: 0,
          right: 0,
          display: "flex",
          justifyContent: "center",
          transform: `scale(${0.6 + logo * 0.4}) rotate(${(1 - logo) * -8}deg)`,
          opacity: logo,
        }}
      >
        <LogoMark size={200} radius={56} />
      </div>

      <div
        style={{
          position: "absolute",
          top: 560,
          left: 64,
          right: 140,
          textAlign: "center",
          opacity: text,
          transform: `translateY(${(1 - text) * 30}px)`,
        }}
      >
        <div
          style={{
            fontSize: 94,
            fontWeight: 800,
            letterSpacing: -3.5,
            lineHeight: 1,
            color: "#ffffff",
          }}
        >
          LAB <span style={GRADIENT_TEXT}>BOOKING</span>
        </div>
        <div
          style={{
            marginTop: 22,
            fontSize: 32,
            fontWeight: 500,
            color: "rgba(255,255,255,0.72)",
          }}
        >
          Booking laboratorium jadi lebih mudah.
        </div>
      </div>

      <div
        style={{
          position: "absolute",
          top: 820,
          left: 0,
          right: 0,
          display: "flex",
          justifyContent: "center",
          opacity: button,
          transform: `translateY(${(1 - button) * 34}px) scale(${wave})`,
        }}
      >
        <div
          style={{
            width: 700,
            height: 124,
            borderRadius: 999,
            background: GRADIENT,
            color: "#ffffff",
            display: "flex",
            alignItems: "center",
            justifyContent: "center",
            gap: 18,
            fontSize: 37,
            fontWeight: 800,
            letterSpacing: -0.5,
            boxShadow: "0 26px 70px rgba(56,189,248,0.35)",
          }}
        >
          Ajukan Booking
          <ArrowRightIcon size={35} color="#ffffff" />
        </div>
      </div>

      <QrCard progress={qr} />

      <div
        style={{
          position: "absolute",
          top: 1420,
          left: 64,
          right: 140,
          display: "flex",
          flexDirection: "column",
          alignItems: "center",
          gap: 8,
          opacity: url,
          transform: `translateY(${(1 - url) * 18}px)`,
        }}
      >
        <div
          style={{
            display: "flex",
            alignItems: "center",
            gap: 8,
            fontSize: 25,
            fontWeight: 700,
            color: "rgba(255,255,255,0.72)",
          }}
        >
          <SparklesIcon size={21} color={COLORS.teal} />
          Scan QR atau buka link di bio
        </div>
        <div
          style={{
            fontSize: 20,
            fontWeight: 600,
            color: "rgba(255,255,255,0.42)",
          }}
        >
          {APP_URL}
        </div>
      </div>

      <div
        style={{
          position: "absolute",
          bottom: 0,
          left: 0,
          right: 0,
          height: 380,
          background:
            "linear-gradient(180deg, rgba(11,21,51,0) 0%, rgba(6,12,31,0.80) 100%)",
          opacity: interpolate(frame, [90, 130], [0, 1], {
            extrapolateLeft: "clamp",
            extrapolateRight: "clamp",
          }),
        }}
      />
    </AbsoluteFill>
  );
};
