import { AbsoluteFill, interpolate, useCurrentFrame, useVideoConfig } from "remotion";
import { BellIcon } from "../components/Icons";
import { PhoneFrame, StatusBar } from "../components/PhoneFrame";
import { BottomNote, Highlight, SceneHeading } from "../components/SceneHeading";
import { COLORS, FONT_FAMILY } from "../theme";

const Bubble: React.FC<{
  children: React.ReactNode;
  from: "me" | "them" | "system";
  progress: number;
}> = ({ children, from, progress }) => {
  if (from === "system") {
    return (
      <div
        style={{
          alignSelf: "center",
          fontSize: 17,
          fontWeight: 600,
          color: COLORS.muted,
          background: "rgba(16,24,40,0.05)",
          padding: "7px 16px",
          borderRadius: 999,
          opacity: progress,
          transform: `translateY(${(1 - progress) * 10}px)`,
          margin: "4px 0",
        }}
      >
        {children}
      </div>
    );
  }

  const mine = from === "me";

  return (
    <div
      style={{
        alignSelf: mine ? "flex-end" : "flex-start",
        maxWidth: "82%",
        background: mine ? COLORS.primary : "#ffffff",
        color: mine ? "#ffffff" : COLORS.ink,
        padding: "15px 20px",
        borderRadius: mine ? "24px 24px 8px 24px" : "24px 24px 24px 8px",
        fontSize: 22,
        lineHeight: 1.4,
        fontWeight: 500,
        boxShadow: "0 4px 14px rgba(16,24,40,0.06)",
        opacity: progress,
        transform: `translateY(${(1 - progress) * 16}px) scale(${0.96 + progress * 0.04})`,
      }}
    >
      {children}
    </div>
  );
};

export const Scene01Hook: React.FC = () => {
  const frame = useCurrentFrame();
  const { fps } = useVideoConfig();

  const shake = frame > 58 ? Math.sin((frame - 58) * 0.9) * 2.4 : 0;
  const typingOut = interpolate(frame, [56, 64], [1, 0], {
    extrapolateLeft: "clamp",
    extrapolateRight: "clamp",
  });

  return (
    <AbsoluteFill
      style={{
        background:
          "radial-gradient(1200px 700px at 80% -10%, #24347a 0%, rgba(11,21,51,0) 60%), radial-gradient(900px 600px at 0% 110%, #0f3f4a 0%, rgba(11,21,51,0) 60%), #0b1533",
        fontFamily: FONT_FAMILY,
        overflow: "hidden",
      }}
    >
      <div
        style={{
          position: "absolute",
          width: 700,
          height: 700,
          borderRadius: "50%",
          border: "2px solid rgba(255,255,255,0.06)",
          top: 120,
          left: -240,
        }}
      />
      <div
        style={{
          position: "absolute",
          width: 900,
          height: 900,
          borderRadius: "50%",
          border: "2px solid rgba(56,189,248,0.10)",
          bottom: -380,
          right: -260,
        }}
      />

      <SceneHeading
        kicker="Masalahnya"
        light
        size={78}
        title={
          <>
            Booking lab,
            <br />
            <Highlight light>balasnya kapan?</Highlight>
          </>
        }
      />

      <div
        style={{
          position: "absolute",
          top: 580,
          left: "50%",
          transform: `translateX(-50%) translateX(${shake}px)`,
        }}
      >
        <PhoneFrame width={430} height={860}>
          <div
            style={{
              height: "100%",
              display: "flex",
              flexDirection: "column",
              background: COLORS.bg,
            }}
          >
            <StatusBar />

            <div
              style={{
                display: "flex",
                alignItems: "center",
                gap: 16,
                padding: "18px 26px 20px",
                background: "#ffffff",
                borderBottom: `1px solid ${COLORS.line}`,
              }}
            >
              <div
                style={{
                  width: 58,
                  height: 58,
                  borderRadius: "50%",
                  background: COLORS.primarySoft,
                  color: COLORS.primary,
                  display: "flex",
                  alignItems: "center",
                  justifyContent: "center",
                  fontSize: 24,
                  fontWeight: 800,
                }}
              >
                AL
              </div>
              <div style={{ flex: 1 }}>
                <div style={{ fontSize: 23, fontWeight: 800, color: COLORS.ink }}>
                  Admin Lab
                </div>
                <div style={{ fontSize: 17, color: COLORS.muted }}>
                  Terakhir aktif 3 jam lalu
                </div>
              </div>
              <div style={{ position: "relative" }}>
                <BellIcon size={28} color={COLORS.ink2} />
                <span
                  style={{
                    position: "absolute",
                    top: -4,
                    right: -4,
                    width: 13,
                    height: 13,
                    borderRadius: "50%",
                    background: COLORS.danger,
                    border: "2px solid #fff",
                    opacity: frame > 24 ? 1 : 0,
                  }}
                />
              </div>
            </div>

            <div
              style={{
                flex: 1,
                display: "flex",
                flexDirection: "column",
                gap: 14,
                padding: "22px 24px",
                overflow: "hidden",
              }}
            >
              <Bubble
                from="me"
                progress={interpolate(frame, [6, 14], [0, 1], {
                  extrapolateLeft: "clamp",
                  extrapolateRight: "clamp",
                })}
              >
                Halo, lab bisa dipakai Rabu jam 10?
              </Bubble>

              <Bubble
                from="me"
                progress={interpolate(frame, [16, 24], [0, 1], {
                  extrapolateLeft: "clamp",
                  extrapolateRight: "clamp",
                })}
              >
                Kalau sore, masih kosong?
              </Bubble>

              <Bubble
                from="system"
                progress={interpolate(frame, [28, 34], [0, 1], {
                  extrapolateLeft: "clamp",
                  extrapolateRight: "clamp",
                })}
              >
                Sudah dibaca · 09:12
              </Bubble>

              <div
                style={{
                  display: "flex",
                  alignItems: "center",
                  gap: 10,
                  alignSelf: "flex-start",
                  background: "#fff",
                  borderRadius: "20px 20px 20px 6px",
                  padding: "14px 18px",
                  boxShadow: "0 4px 14px rgba(16,24,40,0.06)",
                  opacity: typingOut,
                  marginTop: 2,
                }}
              >
                {[0, 1, 2].map((dot) => {
                  const bounce =
                    frame > 40
                      ? Math.max(
                          0,
                          Math.sin((frame - 52) * 0.35 - dot * 0.9),
                        )
                      : 0;
                  return (
                    <span
                      key={dot}
                      style={{
                        width: 11,
                        height: 11,
                        borderRadius: "50%",
                        background: COLORS.muted2,
                        transform: `translateY(${-bounce * 5}px)`,
                      }}
                    />
                  );
                })}
              </div>

              <div
                style={{
                  alignSelf: "center",
                  marginTop: 8,
                  display: "flex",
                  alignItems: "center",
                  gap: 8,
                  background: COLORS.dangerSoft,
                  color: COLORS.danger,
                  padding: "9px 18px",
                  borderRadius: 999,
                  fontSize: 18,
                  fontWeight: 700,
                  opacity: interpolate(frame, [60, 68], [0, 1], {
                    extrapolateLeft: "clamp",
                    extrapolateRight: "clamp",
                  }),
                }}
              >
                <span
                  style={{
                    width: 9,
                    height: 9,
                    borderRadius: "50%",
                    background: COLORS.danger,
                  }}
                />
                Belum ada balasan
              </div>
            </div>

            <div
              style={{
                margin: "0 20px 24px",
                height: 62,
                borderRadius: 999,
                background: "#ffffff",
                border: `1px solid ${COLORS.line}`,
                display: "flex",
                alignItems: "center",
                padding: "0 24px",
                color: COLORS.muted2,
                fontSize: 20,
              }}
            >
              Ketik pesan…
            </div>
          </div>
        </PhoneFrame>
      </div>

      <BottomNote light>Chat lama, jadwal tidak pasti.</BottomNote>
    </AbsoluteFill>
  );
};
