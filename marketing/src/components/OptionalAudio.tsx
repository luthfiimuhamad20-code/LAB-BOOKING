import { Audio, staticFile } from "remotion";
import { staticFileExists } from "../lib/static-files";

/**
 * Audio opsional: otomatis dilewati kalau file belum tersedia.
 * Taruh VO di public/vo/ dan musik di public/audio/.
 */
export const OptionalAudio: React.FC<{
  src: string;
  volume?: number;
  loop?: boolean;
}> = ({ src, volume = 1, loop = false }) => {
  if (!staticFileExists(src)) {
    return null;
  }

  return <Audio src={staticFile(src)} volume={volume} loop={loop} />;
};
