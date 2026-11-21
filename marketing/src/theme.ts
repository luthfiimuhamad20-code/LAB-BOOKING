import type { CSSProperties } from "react";
import { loadFont } from "@remotion/google-fonts/PlusJakartaSans";

const { fontFamily } = loadFont("normal", {
  weights: ["400", "500", "600", "700", "800"],
  subsets: ["latin"],
});

export const FONT_FAMILY = `${fontFamily}, "Segoe UI", system-ui, -apple-system, sans-serif`;

export const COLORS = {
  bg: "#f4f6fb",
  bgDeep: "#eef1f8",
  surface: "#ffffff",
  surfaceSoft: "#f8fafc",
  surfaceTint: "#f5f8ff",
  ink: "#101828",
  ink2: "#344054",
  muted: "#667085",
  muted2: "#98a2b3",
  line: "#e7ebf2",
  primary: "#3b5bfd",
  primaryStrong: "#2945dd",
  primarySoft: "#eef2ff",
  navy: "#0b1533",
  navy2: "#081028",
  teal: "#2dd4bf",
  sky: "#38bdf8",
  success: "#0b8f61",
  successSoft: "#e7f8f0",
  warning: "#b45f06",
  warningSoft: "#fff5e5",
  danger: "#d92d20",
  dangerSoft: "#fff0ee",
} as const;

export const GRADIENT =
  "linear-gradient(135deg, #5b7ffb 0%, #38bdf8 55%, #2dd4bf 100%)";

export const GRADIENT_TEXT: CSSProperties = {
  background: GRADIENT,
  WebkitBackgroundClip: "text",
  backgroundClip: "text",
  color: "transparent",
};

/** Safe area untuk UI TikTok/Reels pada kanvas 1080x1920. */
export const SAFE = {
  top: 220,
  bottom: 420,
  left: 64,
  right: 140,
} as const;

export const SHADOW = {
  card: "0 24px 60px rgba(16, 24, 40, 0.10)",
  phone: "0 40px 90px rgba(11, 21, 51, 0.32)",
  primary: "0 18px 40px rgba(59, 91, 253, 0.30)",
} as const;
