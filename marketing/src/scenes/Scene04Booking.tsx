import { AbsoluteFill, interpolate, useCurrentFrame } from "remotion";
import { AssetScreen } from "../components/AssetScreen";
import {
  AlertIcon,
  CalendarIcon,
  CheckIcon,
  ClockIcon,
} from "../components/Icons";
import { PhoneFrame, StatusBar } from "../components/PhoneFrame";
import { BottomNote, SceneHeading } from "../components/SceneHeading";
import { COLORS, FONT_FAMILY, SHADOW } from "../theme";

const SLOTS = [
  { label: "08.00 – 10.00", conflict: false },
  { label: "10.00 – 12.00", conflict: true },
  { label: "13.00 – 15.00", conflict: false },
];

export const Scene04Booking: React.FC = () => {
  const frame = useCurrentFrame();

  const conflict = frame >= 42 && frame < 80;
  const available = frame >= 80;
  const submitted = frame >= 104;

  const warningIn = interpolate(frame, [42, 50], [0, 1], {
    extrapolateLeft: "clamp",
    extrapolateRight: "clamp",
  });
  const successIn = interpolate(frame, [80, 88], [0, 1], {
    extrapolateLeft: "clamp",
    extrapolateRight: "clamp",
  });
  const toastIn = interpolate(frame, [104, 114], [0, 1], {
    extrapolateLeft: "clamp",
    extrapolateRight: "clamp",
  });

  return (
    <AbsoluteFill
      style={{
        background:
          "radial-gradient(900px 650px at 0% 0%, #eaefff 0%, rgba(244,246,251,0) 55%), radial-gradient(700px 500px at 100% 100%, #e6fffb 0%, rgba(244,246,251,0) 60%), #f4f6fb",
        fontFamily: FONT_FAMILY,
        overflow: "hidden",
      }}
    >
      <SceneHeading
        kicker="4b · Booking"
        size={70}
        title="Cek slot bentrok otomatis"
      />

      <div
        style={{
          position: "absolute",
          top: 520,
          left: "50%",
          transform: "translateX(-50%)",
        }}
      >
        <PhoneFrame width={440} height={940}>
          <AssetScreen file="assets/booking.png">
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
                  gap: 12,
                  padding: "16px 24px 18px",
                  background: "#ffffff",
                  borderBottom: `1px solid ${COLORS.line}`,
                }}
              >
                <span style={{ fontSize: 26, color: COLORS.ink2 }}>‹</span>
                <div
                  style={{
                    fontSize: 24,
                    fontWeight: 800,
                    letterSpacing: -0.6,
                    color: COLORS.ink,
                  }}
                >
                  Ajukan Booking
                </div>
              </div>

              <div
                style={{
                  flex: 1,
                  padding: "18px 22px",
                  display: "flex",
                  flexDirection: "column",
                  gap: 14,
                  overflow: "hidden",
                }}
              >
                <div
                  style={{
                    background: "#ffffff",
                    border: `1px solid ${COLORS.line}`,
                    borderRadius: 16,
                    padding: "14px 16px",
                  }}
                >
                  <div
                    style={{
                      fontSize: 13,
                      fontWeight: 700,
                      color: COLORS.muted,
                      marginBottom: 4,
                    }}
                  >
                    Laboratorium
                  </div>
                  <div
                    style={{
                      fontSize: 20,
                      fontWeight: 800,
                      color: COLORS.ink,
                    }}
                  >
                    Lab Multimedia{" "}
                    <span
                      style={{
                        fontSize: 14,
                        fontWeight: 700,
                        color: COLORS.primary,
                        marginLeft: 6,
                      }}
                    >
                      LM-01
                    </span>
                  </div>
                </div>

                <div
                  style={{
                    display: "flex",
                    gap: 10,
                  }}
                >
                  <div
                    style={{
                      flex: 1,
                      background: "#ffffff",
                      border: `1px solid ${COLORS.line}`,
                      borderRadius: 16,
                      padding: "13px 14px",
                    }}
                  >
                    <div
                      style={{
                        fontSize: 13,
                        fontWeight: 700,
                        color: COLORS.muted,
                        display: "flex",
                        alignItems: "center",
                        gap: 5,
                        marginBottom: 4,
                      }}
                    >
                      <CalendarIcon size={14} color={COLORS.muted} />
                      Tanggal
                    </div>
                    <div
                      style={{
                        fontSize: 17,
                        fontWeight: 800,
                        color: COLORS.ink,
                      }}
                    >
                      Rabu, 18 Nov
                    </div>
                  </div>
                  <div
                    style={{
                      width: 132,
                      background: "#ffffff",
                      border: `1px solid ${COLORS.line}`,
                      borderRadius: 16,
                      padding: "13px 14px",
                    }}
                  >
                    <div
                      style={{
                        fontSize: 13,
                        fontWeight: 700,
                        color: COLORS.muted,
                        display: "flex",
                        alignItems: "center",
                        gap: 5,
                        marginBottom: 4,
                      }}
                    >
                      <ClockIcon size={14} color={COLORS.muted} />
                      Durasi
                    </div>
                    <div
                      style={{
                        fontSize: 17,
                        fontWeight: 800,
                        color: COLORS.ink,
                      }}
                    >
                      2 jam
                    </div>
                  </div>
                </div>

                <div
                  style={{
                    background: "#ffffff",
                    border: `1px solid ${COLORS.line}`,
                    borderRadius: 16,
                    padding: "14px 16px 16px",
                  }}
                >
                  <div
                    style={{
                      fontSize: 14,
                      fontWeight: 800,
                      color: COLORS.ink2,
                      marginBottom: 10,
                    }}
                  >
                    Pilih jam mulai
                  </div>
                  <div
                    style={{ display: "flex", flexDirection: "column", gap: 8 }}
                  >
                    {SLOTS.map((slot, index) => {
                      const isConflict = conflict && slot.conflict;
                      const isPicked =
                        (available && index === 2) ||
                        (conflict && slot.conflict);

                      const bg = isConflict
                        ? COLORS.dangerSoft
                        : isPicked
                          ? COLORS.successSoft
                          : COLORS.surfaceSoft;
                      const border = isConflict
                        ? COLORS.danger
                        : isPicked
                          ? COLORS.success
                          : COLORS.line;
                      const color = isConflict
                        ? COLORS.danger
                        : isPicked
                          ? COLORS.success
                          : COLORS.ink2;

                      return (
                        <div
                          key={slot.label}
                          style={{
                            height: 46,
                            borderRadius: 11,
                            border: `2px solid ${border}`,
                            background: bg,
                            display: "flex",
                            alignItems: "center",
                            padding: "0 14px",
                            gap: 10,
                            fontSize: 17,
                            fontWeight: 700,
                            color,
                          }}
                        >
                          <span
                            style={{
                              width: 16,
                              height: 16,
                              borderRadius: "50%",
                              border: `2px solid ${border}`,
                              display: "flex",
                              alignItems: "center",
                              justifyContent: "center",
                              background: isPicked ? border : "transparent",
                            }}
                          >
                            {isPicked ? (
                              <span
                                style={{
                                  width: 6,
                                  height: 6,
                                  borderRadius: "50%",
                                  background: "#fff",
                                }}
                              />
                            ) : null}
                          </span>
                          {slot.label}
                          {isConflict ? (
                            <span
                              style={{
                                marginLeft: "auto",
                                fontSize: 12,
                                fontWeight: 800,
                                letterSpacing: 0.4,
                              }}
                            >
                              BENTROK
                            </span>
                          ) : null}
                          {isPicked && !isConflict ? (
                            <span style={{ marginLeft: "auto" }}>
                              <CheckIcon size={18} color={COLORS.success} />
                            </span>
                          ) : null}
                        </div>
                      );
                    })}
                  </div>
                </div>

                <div
                  style={{
                    display: "flex",
                    gap: 10,
                  }}
                >
                  <div
                    style={{
                      flex: 1,
                      background: "#ffffff",
                      border: `1px solid ${COLORS.line}`,
                      borderRadius: 14,
                      padding: "10px 13px",
                    }}
                  >
                    <div
                      style={{
                        fontSize: 11,
                        fontWeight: 700,
                        color: COLORS.muted,
                        marginBottom: 2,
                      }}
                    >
                      Peserta
                    </div>
                    <div
                      style={{
                        fontSize: 16,
                        fontWeight: 800,
                        color: COLORS.ink,
                      }}
                    >
                      28 orang
                    </div>
                  </div>
                  <div
                    style={{
                      flex: 1.6,
                      background: "#ffffff",
                      border: `1px solid ${COLORS.line}`,
                      borderRadius: 14,
                      padding: "10px 13px",
                    }}
                  >
                    <div
                      style={{
                        fontSize: 11,
                        fontWeight: 700,
                        color: COLORS.muted,
                        marginBottom: 2,
                      }}
                    >
                      Tujuan
                    </div>
                    <div
                      style={{
                        fontSize: 16,
                        fontWeight: 800,
                        color: COLORS.ink,
                      }}
                    >
                      Praktikum Desain Konten
                    </div>
                  </div>
                </div>

                <div
                  style={{
                    background: "#ffffff",
                    border: `1px solid ${COLORS.line}`,
                    borderRadius: 14,
                    padding: "10px 13px",
                  }}
                >
                  <div
                    style={{
                      fontSize: 11,
                      fontWeight: 700,
                      color: COLORS.muted,
                      marginBottom: 2,
                    }}
                  >
                    Catatan (opsional)
                  </div>
                  <div style={{ fontSize: 14, color: COLORS.muted2 }}>
                    Tambahkan kebutuhan khusus…
                  </div>
                </div>

                <div style={{ minHeight: 58 }}>
                  {frame >= 42 && frame < 80 ? (
                    <div
                      style={{
                        display: "flex",
                        alignItems: "center",
                        gap: 8,
                        background: COLORS.dangerSoft,
                        border: `1px solid rgba(217,45,32,0.2)`,
                        borderRadius: 12,
                        padding: "10px 13px",
                        fontSize: 14,
                        fontWeight: 700,
                        color: COLORS.danger,
                        opacity: warningIn,
                        transform: `translateY(${(1 - warningIn) * 8}px)`,
                      }}
                    >
                      <AlertIcon size={18} color={COLORS.danger} />
                      Jadwal bentrok. Pilih slot lain.
                    </div>
                  ) : null}
                  {available && !submitted ? (
                    <div
                      style={{
                        display: "flex",
                        alignItems: "center",
                        gap: 8,
                        background: COLORS.successSoft,
                        border: `1px solid rgba(11,143,97,0.2)`,
                        borderRadius: 12,
                        padding: "10px 13px",
                        fontSize: 14,
                        fontWeight: 700,
                        color: COLORS.success,
                        opacity: successIn,
                        transform: `translateY(${(1 - successIn) * 8}px)`,
                      }}
                    >
                      <CheckIcon size={18} color={COLORS.success} />
                      Slot tersedia. Lanjutkan pengajuan.
                    </div>
                  ) : null}
                  {submitted ? (
                    <div
                      style={{
                        display: "flex",
                        alignItems: "center",
                        gap: 8,
                        background: COLORS.successSoft,
                        border: `1px solid rgba(11,143,97,0.2)`,
                        borderRadius: 12,
                        padding: "10px 13px",
                        fontSize: 14,
                        fontWeight: 700,
                        color: COLORS.success,
                        opacity: toastIn,
                      }}
                    >
                      <CheckIcon size={18} color={COLORS.success} />
                      Booking dikirim · status Menunggu
                    </div>
                  ) : null}
                </div>

                <div
                  style={{
                    marginTop: "auto",
                    height: 54,
                    borderRadius: 14,
                    background: submitted ? COLORS.success : COLORS.primary,
                    color: "#fff",
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "center",
                    gap: 8,
                    fontSize: 18,
                    fontWeight: 800,
                    boxShadow: submitted
                      ? "0 12px 26px rgba(11,143,97,0.28)"
                      : SHADOW.primary,
                    transform: `scale(${frame >= 100 && frame < 108 ? 0.97 : 1})`,
                  }}
                >
                  {submitted ? (
                    <>
                      <CheckIcon size={20} color="#fff" />
                      Booking Terkirim
                    </>
                  ) : (
                    "Ajukan Booking"
                  )}
                </div>
              </div>
            </div>
          </AssetScreen>
        </PhoneFrame>
      </div>

      <BottomNote>Validasi kapasitas dan jam operasional otomatis.</BottomNote>
    </AbsoluteFill>
  );
};
