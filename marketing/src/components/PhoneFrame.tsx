import type { CSSProperties, ReactNode } from "react";
import { COLORS, SHADOW } from "../theme";

export const PhoneFrame: React.FC<{
  children: ReactNode;
  width?: number;
  height?: number;
  style?: CSSProperties;
  screenStyle?: CSSProperties;
  glow?: boolean;
}> = ({
  children,
  width = 436,
  height = 940,
  style,
  screenStyle,
  glow = true,
}) => {
  return (
    <div
      style={{
        width,
        height,
        borderRadius: width * 0.14,
        padding: width * 0.026,
        background: "linear-gradient(160deg, #26325c 0%, #0b1533 55%, #060c1f 100%)",
        boxShadow: glow ? SHADOW.phone : "0 18px 40px rgba(11, 21, 51, 0.18)",
        position: "relative",
        flex: "none",
        ...style,
      }}
    >
      <div
        style={{
          width: "100%",
          height: "100%",
          borderRadius: width * 0.115,
          overflow: "hidden",
          background: COLORS.bg,
          position: "relative",
          ...screenStyle,
        }}
      >
        {children}
        <div
          style={{
            position: "absolute",
            top: 10,
            left: "50%",
            transform: "translateX(-50%)",
            width: width * 0.34,
            height: width * 0.085,
            borderRadius: 999,
            background: "#040814",
            zIndex: 40,
          }}
        />
      </div>
    </div>
  );
};

export const StatusBar: React.FC<{ color?: string }> = ({
  color = COLORS.ink,
}) => (
  <div
    style={{
      display: "flex",
      alignItems: "center",
      justifyContent: "space-between",
      padding: "18px 30px 6px",
      fontSize: 20,
      fontWeight: 700,
      color,
      opacity: 0.85,
    }}
  >
    <span>9:41</span>
    <div style={{ display: "flex", alignItems: "center", gap: 8 }}>
      <span style={{ width: 22, height: 12, borderRadius: 3, border: `2px solid ${color}`, display: "inline-block" }} />
      <span style={{ width: 18, height: 12, borderRadius: 2, background: color, display: "inline-block" }} />
      <span style={{ width: 26, height: 12, borderRadius: 3, background: color, display: "inline-block" }} />
    </div>
  </div>
);
