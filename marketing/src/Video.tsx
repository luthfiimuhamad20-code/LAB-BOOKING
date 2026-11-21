import type { FC } from "react";
import { AbsoluteFill, Sequence } from "remotion";
import { OptionalAudio } from "./components/OptionalAudio";
import { SCENES, type SceneId } from "./script";
import { Scene01Hook } from "./scenes/Scene01Hook";
import { Scene02Problem } from "./scenes/Scene02Problem";
import { Scene03Solution } from "./scenes/Scene03Solution";
import { Scene04Agenda } from "./scenes/Scene04Agenda";
import { Scene04Approval } from "./scenes/Scene04Approval";
import { Scene04Booking } from "./scenes/Scene04Booking";
import { Scene04Katalog } from "./scenes/Scene04Katalog";
import { Scene05Benefits } from "./scenes/Scene05Benefits";
import { Scene06CTA } from "./scenes/Scene06CTA";
import { COLORS, FONT_FAMILY } from "./theme";

const SCENE_COMPONENTS: Record<SceneId, FC> = {
  hook: Scene01Hook,
  problem: Scene02Problem,
  solution: Scene03Solution,
  katalog: Scene04Katalog,
  booking: Scene04Booking,
  approval: Scene04Approval,
  agenda: Scene04Agenda,
  benefits: Scene05Benefits,
  cta: Scene06CTA,
};

export const MarketingVideo: FC = () => {
  return (
    <AbsoluteFill
      style={{
        backgroundColor: COLORS.bg,
        fontFamily: FONT_FAMILY,
      }}
    >
      {/* Musik opsional: taruh file di public/audio/music.mp3 */}
      <OptionalAudio src="audio/music.mp3" volume={0.12} loop />

      {SCENES.map((scene) => {
        const SceneComponent = SCENE_COMPONENTS[scene.id];

        return (
          <Sequence
            key={scene.id}
            name={scene.label}
            from={scene.from}
            durationInFrames={scene.duration}
          >
            {/* VO per scene: public/vo/01-hook.mp3, dst. */}
            <OptionalAudio src={scene.voFile} volume={1} />
            <SceneComponent />
          </Sequence>
        );
      })}
    </AbsoluteFill>
  );
};
