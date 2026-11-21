import { getStaticFiles } from "remotion";

let cache: Set<string> | null = null;

/**
 * Cek apakah file ada di folder public/.
 * Dipakai supaya komposisi tetap bisa dirender walau VO / screenshot belum ada.
 */
export const staticFileExists = (name: string): boolean => {
  try {
    if (!cache) {
      cache = new Set(getStaticFiles().map((file) => file.name));
    }
    return cache.has(name);
  } catch {
    return false;
  }
};
