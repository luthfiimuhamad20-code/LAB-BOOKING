import type { CSSProperties, ReactNode } from "react";
import { Img, staticFile } from "remotion";
import { staticFileExists } from "../lib/static-files";

/**
 * Menampilkan screenshot asli kalau tersedia di public/assets/.
 * Kalau belum ada, mock UI di dalamnya tetap tampil.
 */
export const AssetScreen: React.FC<{
  file: string;
  children: ReactNode;
  style?: CSSProperties;
  fit?: CSSProperties["objectFit"];
}> = ({ file, children, style, fit = "cover" }) => {
  if (!staticFileExists(file)) {
    return <>{children}</>;
  }

  return (
    <Img
      src={staticFile(file)}
      style={{
        width: "100%",
        height: "100%",
        objectFit: fit,
        display: "block",
        ...style,
      }}
    />
  );
};
