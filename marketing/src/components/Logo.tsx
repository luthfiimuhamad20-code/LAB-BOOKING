import type { CSSProperties, ReactNode } from "react";
import { COLORS, FONT_FAMILY, GRADIENT } from "../theme";

export const LogoMark: React.FC<{
  size?: number;
  radius?: number;
  style?: CSSProperties;
}> = ({ size = 120, radius = 32, style }) => {
  return (
    <div
      style={{
        width: size,
        height: size,
        borderRadius: radius,
        background: GRADIENT,
        display: "flex",
        alignItems: "center",
        justifyContent: "center",
        boxShadow: "0 22px 50px rgba(59, 91, 253, 0.32)",
        flex: "none",
        ...style,
      }}
    >
      <svg
        width={size * 0.56}
        height={size * 0.56}
        viewBox="0 0 24 24"
        fill="none"
      >
        <rect
          x="3"
          y="4.5"
          width="18"
          height="16"
          rx="4"
          stroke="white"
          strokeWidth="2"
        />
        <path
          d="M3 9.5h18M8 2.5v4M16 2.5v4"
          stroke="white"
          strokeWidth="2"
          strokeLinecap="round"
        />
        <path
          d="M8 13.5h4.5M8 17h8"
          stroke="white"
          strokeWidth="2"
          strokeLinecap="round"
        />
      </svg>
    </div>
  );
};

export const LogoLockup: React.FC<{
  markSize?: number;
  color?: string;
  gap?: number;
}> = ({ markSize = 96, color = COLORS.ink, gap = 20 }) => {
  return (
    <div style={{ display: "flex", alignItems: "center", gap }}>
      <LogoMark size={markSize} radius={markSize * 0.28} />
      <div
        style={{
          display: "flex",
          flexDirection: "column",
          justifyContent: "center",
          lineHeight: 1,
          fontFamily: FONT_FAMILY,
        }}
      >
        <span
          style={{
            fontSize: markSize * 0.25,
            letterSpacing: markSize * 0.12,
            fontWeight: 700,
            color,
            opacity: 0.65,
            marginBottom: markSize * 0.06,
          }}
        >
          LAB
        </span>
        <span
          style={{
            fontSize: markSize * 0.42,
            letterSpacing: -markSize * 0.008,
            fontWeight: 800,
            color,
          }}
        >
          BOOKING
        </span>
      </div>
    </div>
  );
};

export const Pill: React.FC<{
  children: ReactNode;
  tone?: "primary" | "success" | "danger" | "neutral" | "warning";
  style?: CSSProperties;
}> = ({ children, tone = "primary", style }) => {
  const tones = {
    primary: { bg: COLORS.primarySoft, fg: COLORS.primaryStrong },
    success: { bg: COLORS.successSoft, fg: COLORS.success },
    danger: { bg: COLORS.dangerSoft, fg: COLORS.danger },
    neutral: { bg: "#f1f3f7", fg: COLORS.muted },
    warning: { bg: COLORS.warningSoft, fg: COLORS.warning },
  } as const;

  const { bg, fg } = tones[tone];

  return (
    <span
      style={{
        display: "inline-flex",
        alignItems: "center",
        gap: 8,
        background: bg,
        color: fg,
        borderRadius: 999,
        padding: "8px 16px",
        fontSize: 22,
        fontWeight: 700,
        letterSpacing: 0.2,
        whiteSpace: "nowrap",
        ...style,
      }}
    >
      {children}
    </span>
  );
};
