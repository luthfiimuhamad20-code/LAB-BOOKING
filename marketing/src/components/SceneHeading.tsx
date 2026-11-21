import type { CSSProperties, ReactNode } from "react";
import { COLORS, FONT_FAMILY, GRADIENT_TEXT, SAFE } from "../theme";

export const SceneHeading: React.FC<{
  kicker?: string;
  title: ReactNode;
  light?: boolean;
  top?: number;
  size?: number;
  align?: "left" | "center";
  style?: CSSProperties;
}> = ({
  kicker,
  title,
  light = false,
  top = SAFE.top,
  size = 72,
  align = "left",
  style,
}) => {
  return (
    <div
      style={{
        position: "absolute",
        top,
        left: SAFE.left,
        right: SAFE.right,
        display: "flex",
        flexDirection: "column",
        alignItems: align === "center" ? "center" : "flex-start",
        textAlign: align,
        fontFamily: FONT_FAMILY,
        zIndex: 20,
        ...style,
      }}
    >
      {kicker ? (
        <div
          style={{
            display: "inline-flex",
            alignItems: "center",
            gap: 12,
            fontSize: 24,
            fontWeight: 800,
            letterSpacing: 4,
            textTransform: "uppercase",
            marginBottom: 22,
            color: light ? COLORS.teal : COLORS.primary,
          }}
        >
          <span
            style={{
              width: 34,
              height: 4,
              borderRadius: 999,
              background: light ? COLORS.teal : COLORS.primary,
            }}
          />
          {kicker}
        </div>
      ) : null}
      <h1
        style={{
          margin: 0,
          fontSize: size,
          lineHeight: 1.06,
          fontWeight: 800,
          letterSpacing: -2.2,
          color: light ? "#ffffff" : COLORS.ink,
          textWrap: "balance",
        }}
      >
        {title}
      </h1>
    </div>
  );
};

export const Highlight: React.FC<{ children: ReactNode; light?: boolean }> = ({
  children,
  light = false,
}) => (
  <span
    style={
      light
        ? {
            background: "linear-gradient(135deg, #38bdf8 0%, #2dd4bf 100%)",
            WebkitBackgroundClip: "text",
            backgroundClip: "text",
            color: "transparent",
          }
        : GRADIENT_TEXT
    }
  >
    {children}
  </span>
);

export const BottomNote: React.FC<{
  children: ReactNode;
  light?: boolean;
}> = ({ children, light = false }) => (
  <div
    style={{
      position: "absolute",
      bottom: SAFE.bottom - 30,
      left: SAFE.left,
      right: SAFE.right,
      textAlign: "center",
      fontSize: 28,
      fontWeight: 600,
      letterSpacing: 0.2,
      color: light ? "rgba(255,255,255,0.72)" : COLORS.muted,
      fontFamily: FONT_FAMILY,
      zIndex: 20,
    }}
  >
    {children}
  </div>
);
