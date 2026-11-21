import { interpolate, spring } from "remotion";

/** Muncul dengan scale + fade. */
export const popIn = (frame: number, fps: number, delay = 0, damping = 16) => {
  const s = spring({
    frame: frame - delay,
    fps,
    config: { damping, mass: 0.6, stiffness: 130 },
  });

  return {
    opacity: interpolate(s, [0, 1], [0, 1]),
    transform: `scale(${interpolate(s, [0, 1], [0.86, 1])})`,
  };
};

/** Muncul dari bawah dengan spring halus. */
export const riseIn = (frame: number, fps: number, delay = 0, distance = 48) => {
  const s = spring({
    frame: frame - delay,
    fps,
    config: { damping: 200, mass: 0.8, stiffness: 110 },
  });

  return {
    opacity: s,
    transform: `translateY(${(1 - s) * distance}px)`,
  };
};

/** Fade linear dengan clamp. */
export const fade = (
  frame: number,
  start: number,
  end: number,
  from = 0,
  to = 1,
) =>
  interpolate(frame, [start, end], [from, to], {
    extrapolateLeft: "clamp",
    extrapolateRight: "clamp",
  });
