import { existsSync } from "node:fs";
import { Config } from "@remotion/cli/config";

Config.setVideoImageFormat("jpeg");
Config.setOverwriteOutput(true);
Config.setConcurrency(2);

/**
 * Pakai Chrome/Edge yang sudah terpasang supaya tidak perlu mengunduh
 * Chrome Headless Shell saat render.
 */
const candidates = [
  process.env.REMOTION_BROWSER_EXECUTABLE,
  "C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe",
  "C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe",
].filter((value): value is string => Boolean(value));

const browserExecutable = candidates.find((candidate) => existsSync(candidate));

if (browserExecutable) {
  Config.setBrowserExecutable(browserExecutable);
}
