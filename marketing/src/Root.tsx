import type { FC } from "react";
import { AbsoluteFill, Composition } from "remotion";
import { FPS, HEIGHT, TOTAL_FRAMES, WIDTH } from "./script";
import { MarketingVideo } from "./Video";
import { COLORS } from "./theme";

const LANDSCAPE_SCALE = 0.5625; // 1080x1920 -> 607.5x1080

const LandscapeVideo: FC = () => {
  return (
    <AbsoluteFill
      style={{
        backgroundColor: COLORS.navy,
        alignItems: "center",
        justifyContent: "center",
        overflow: "hidden",
      }}
    >
      <div
        style={{
          width: WIDTH,
          height: HEIGHT,
          flex: "none",
          position: "relative",
          transform: `scale(${LANDSCAPE_SCALE})`,
          transformOrigin: "center center",
          borderRadius: 0,
          overflow: "hidden",
        }}
      >
        <MarketingVideo />
      </div>
    </AbsoluteFill>
  );
};

export const RemotionRoot: FC = () => {
  return (
    <>
      <Composition
        id="MarketingVertical"
        component={MarketingVideo}
        durationInFrames={TOTAL_FRAMES}
        fps={FPS}
        width={WIDTH}
        height={HEIGHT}
      />
      <Composition
        id="MarketingLandscape"
        component={LandscapeVideo}
        durationInFrames={TOTAL_FRAMES}
        fps={FPS}
        width={1920}
        height={1080}
      />
    </>
  );
};
