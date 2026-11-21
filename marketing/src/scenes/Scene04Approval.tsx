import { AbsoluteFill, interpolate, useCurrentFrame } from "remotion";
import { AssetScreen } from "../components/AssetScreen";
import {
  CalendarIcon,
  CheckIcon,
  ClockIcon,
  UsersIcon,
  XIcon,
} from "../components/Icons";
import { PhoneFrame, StatusBar } from "../components/PhoneFrame";
import { Pill } from "../components/Logo";
import { BottomNote, SceneHeading } from "../components/SceneHeading";
import { COLORS, FONT_FAMILY, SHADOW } from "../theme";

export const Scene04Approval: React.FC = () => {
  const frame = useCurrentFrame();

  const pressing = frame >= 24 && frame < 32;
  const approved = frame >= 32;

  const noteIn = interpolate(frame, [36, 48], [0, 1], {
    extrapolateLeft: "clamp",
    extrapolateRight: "clamp",
  });
  const statusIn = interpolate(frame, [30, 40], [0, 1], {
    extrapolateLeft: "clamp",
    extrapolateRight: "clamp",
  });

  return (
    <AbsoluteFill
      style={{
        background:
          "radial-gradient(900px 650px at 100% 0%, #e9fff8 0%, rgba(244,246,251,0) 55%), #f4f6fb",
        fontFamily: FONT_FAMILY,
        overflow: "hidden",
      }}
    >
      <SceneHeading
        kicker="4c · Persetujuan"
        size={68}
        title={
          <>
            Admin menyetujui,
            <br />
            lengkap dengan catatan.
          </>
        }
      />

      <div
        style={{
          position: "absolute",
          top: 600,
          left: "50%",
          transform: "translateX(-50%)",
        }}
      >
        <PhoneFrame width={440} height={880}>
          <AssetScreen file="assets/approval.png">
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
                  padding: "16px 24px 18px",
                  background: "#ffffff",
                  borderBottom: `1px solid ${COLORS.line}`,
                }}
              >
                <div
                  style={{
                    fontSize: 24,
                    fontWeight: 800,
                    letterSpacing: -0.6,
                    color: COLORS.ink,
                  }}
                >
                  Pengajuan Masuk
                </div>
                <div
                  style={{ fontSize: 14, color: COLORS.muted, marginTop: 2 }}
                >
                  3 pengajuan menunggu tinjauan
                </div>
              </div>

              <div
                style={{
                  flex: 1,
                  padding: "18px 20px",
                  display: "flex",
                  flexDirection: "column",
                  gap: 14,
                  overflow: "hidden",
                }}
              >
                <div
                  style={{
                    background: "#ffffff",
                    borderRadius: 20,
                    border: `1px solid ${COLORS.line}`,
                    padding: "18px 18px",
                    boxShadow: "0 8px 22px rgba(16,24,40,0.05)",
                  }}
                >
                  <div
                    style={{
                      display: "flex",
                      alignItems: "center",
                      gap: 11,
                      marginBottom: 12,
                    }}
                  >
                    <div
                      style={{
                        width: 42,
                        height: 42,
                        borderRadius: "50%",
                        background: COLORS.primarySoft,
                        color: COLORS.primary,
                        display: "flex",
                        alignItems: "center",
                        justifyContent: "center",
                        fontWeight: 800,
                        fontSize: 17,
                      }}
                    >
                      RA
                    </div>
                    <div style={{ flex: 1 }}>
                      <div
                        style={{
                          fontSize: 18,
                          fontWeight: 800,
                          color: COLORS.ink,
                        }}
                      >
                        Rani Aulia
                      </div>
                      <div style={{ fontSize: 13, color: COLORS.muted }}>
                        Pendidikan Multimedia
                      </div>
                    </div>
                    {approved ? (
                      <Pill
                        tone="success"
                        style={{ fontSize: 13, padding: "5px 11px", opacity: statusIn }}
                      >
                        <CheckIcon size={13} color={COLORS.success} />
                        Disetujui
                      </Pill>
                    ) : (
                      <Pill tone="warning" style={{ fontSize: 13, padding: "5px 11px" }}>
                        Menunggu
                      </Pill>
                    )}
                  </div>

                  <div
                    style={{
                      fontSize: 20,
                      fontWeight: 800,
                      color: COLORS.ink,
                      marginBottom: 8,
                    }}
                  >
                    Lab Multimedia
                  </div>

                  <div
                    style={{
                      display: "flex",
                      flexDirection: "column",
                      gap: 6,
                      fontSize: 14,
                      color: COLORS.muted,
                      fontWeight: 600,
                    }}
                  >
                    <span
                      style={{ display: "flex", alignItems: "center", gap: 7 }}
                    >
                      <CalendarIcon size={15} color={COLORS.muted} />
                      Rabu, 18 Nov 2026
                    </span>
                    <span
                      style={{ display: "flex", alignItems: "center", gap: 7 }}
                    >
                      <ClockIcon size={15} color={COLORS.muted} />
                      13.00 – 15.00
                    </span>
                    <span
                      style={{ display: "flex", alignItems: "center", gap: 7 }}
                    >
                      <UsersIcon size={15} color={COLORS.muted} />
                      28 peserta · Praktikum Desain Konten
                    </span>
                  </div>

                  {!approved ? (
                    <div style={{ display: "flex", gap: 10, marginTop: 16 }}>
                      <div
                        style={{
                          flex: 1,
                          height: 46,
                          borderRadius: 12,
                          border: `1.5px solid ${COLORS.line}`,
                          color: COLORS.ink2,
                          display: "flex",
                          alignItems: "center",
                          justifyContent: "center",
                          gap: 6,
                          fontSize: 15,
                          fontWeight: 800,
                          background: "#fff",
                        }}
                      >
                        <XIcon size={16} color={COLORS.ink2} />
                        Tolak
                      </div>
                      <div
                        style={{
                          flex: 1.35,
                          height: 46,
                          borderRadius: 12,
                          background: COLORS.success,
                          color: "#fff",
                          display: "flex",
                          alignItems: "center",
                          justifyContent: "center",
                          gap: 6,
                          fontSize: 15,
                          fontWeight: 800,
                          boxShadow: "0 10px 22px rgba(11,143,97,0.25)",
                          transform: `scale(${pressing ? 0.94 : 1})`,
                        }}
                      >
                        <CheckIcon size={17} color="#fff" />
                        Setujui
                      </div>
                    </div>
                  ) : (
                    <div
                      style={{
                        marginTop: 16,
                        background: COLORS.successSoft,
                        border: "1px solid rgba(11,143,97,0.18)",
                        borderRadius: 14,
                        padding: "12px 14px",
                        opacity: noteIn,
                        transform: `translateY(${(1 - noteIn) * 8}px)`,
                      }}
                    >
                      <div
                        style={{
                          fontSize: 12,
                          fontWeight: 800,
                          letterSpacing: 0.6,
                          color: COLORS.success,
                          textTransform: "uppercase",
                          marginBottom: 4,
                        }}
                      >
                        Catatan admin
                      </div>
                      <div
                        style={{
                          fontSize: 14,
                          fontWeight: 600,
                          color: COLORS.ink2,
                          lineHeight: 1.4,
                        }}
                      >
                        Disetujui. Gunakan lab sesuai jadwal dan jaga perangkat
                        tetap rapi.
                      </div>
                    </div>
                  )}
                </div>

                <div
                  style={{
                    display: "flex",
                    alignItems: "center",
                    gap: 10,
                    background: "#ffffff",
                    border: `1px solid ${COLORS.line}`,
                    borderRadius: 16,
                    padding: "12px 14px",
                    opacity: 0.8,
                  }}
                >
                  <div
                    style={{
                      width: 36,
                      height: 36,
                      borderRadius: "50%",
                      background: "#f1f3f7",
                      color: COLORS.muted,
                      display: "flex",
                      alignItems: "center",
                      justifyContent: "center",
                      fontWeight: 800,
                      fontSize: 14,
                    }}
                  >
                    BS
                  </div>
                  <div style={{ flex: 1 }}>
                    <div
                      style={{
                        fontSize: 16,
                        fontWeight: 800,
                        color: COLORS.ink,
                      }}
                    >
                      Budi Santoso
                    </div>
                    <div style={{ fontSize: 12, color: COLORS.muted }}>
                      Lab Jaringan · 10.00–12.00
                    </div>
                  </div>
                  <Pill
                    tone="warning"
                    style={{ fontSize: 11, padding: "4px 9px" }}
                  >
                    Menunggu
                  </Pill>
                </div>

                <div
                  style={{
                    background: "#ffffff",
                    borderRadius: 18,
                    border: `1px solid ${COLORS.line}`,
                    padding: "16px 18px",
                    marginTop: "auto",
                  }}
                >
                  <div
                    style={{
                      fontSize: 13,
                      fontWeight: 800,
                      color: COLORS.ink2,
                      marginBottom: 12,
                    }}
                  >
                    Riwayat status
                  </div>
                  <div
                    style={{
                      display: "flex",
                      alignItems: "center",
                      gap: 8,
                    }}
                  >
                    <span
                      style={{
                        fontSize: 12,
                        fontWeight: 800,
                        color: COLORS.muted,
                        background: "#f1f3f7",
                        padding: "6px 10px",
                        borderRadius: 999,
                      }}
                    >
                      Menunggu
                    </span>
                    <span style={{ color: COLORS.muted2, fontSize: 16 }}>→</span>
                    <span
                      style={{
                        fontSize: 12,
                        fontWeight: 800,
                        color: approved ? COLORS.success : COLORS.muted2,
                        background: approved
                          ? COLORS.successSoft
                          : "#f1f3f7",
                        padding: "6px 10px",
                        borderRadius: 999,
                      }}
                    >
                      Disetujui
                    </span>
                    <span
                      style={{
                        marginLeft: "auto",
                        fontSize: 12,
                        color: COLORS.muted2,
                      }}
                    >
                      baru saja
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </AssetScreen>
        </PhoneFrame>
      </div>

      <BottomNote>Keputusan admin transparan untuk pemohon.</BottomNote>
    </AbsoluteFill>
  );
};
