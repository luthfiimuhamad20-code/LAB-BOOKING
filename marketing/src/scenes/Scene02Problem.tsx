import { AbsoluteFill, interpolate, useCurrentFrame } from "remotion";
import { AlertIcon, ClockIcon, XIcon } from "../components/Icons";
import { BottomNote, Highlight, SceneHeading } from "../components/SceneHeading";
import { COLORS, FONT_FAMILY, SHADOW } from "../theme";

const CutShell: React.FC<{
  children: React.ReactNode;
  local: number;
}> = ({ children, local }) => {
  const p = interpolate(local, [0, 8], [0, 1], {
    extrapolateLeft: "clamp",
    extrapolateRight: "clamp",
  });

  return (
    <div
      style={{
        position: "absolute",
        inset: 0,
        padding: "44px 44px 40px",
        display: "flex",
        flexDirection: "column",
        opacity: 1,
        transform: `scale(${0.985 + p * 0.015})`,
      }}
    >
      {children}
    </div>
  );
};

const ChatIssue: React.FC<{ local: number }> = ({ local }) => (
  <CutShell local={local}>
    <div style={{ display: "flex", alignItems: "center", gap: 14 }}>
      <div
        style={{
          width: 54,
          height: 54,
          borderRadius: "50%",
          background: COLORS.primarySoft,
          color: COLORS.primary,
          display: "flex",
          alignItems: "center",
          justifyContent: "center",
          fontWeight: 800,
          fontSize: 22,
        }}
      >
        AL
      </div>
      <div style={{ flex: 1 }}>
        <div style={{ fontSize: 24, fontWeight: 800, color: COLORS.ink }}>
          Admin Lab
        </div>
        <div style={{ fontSize: 17, color: COLORS.muted }}>online</div>
      </div>
      <span
        style={{
          fontSize: 17,
          fontWeight: 700,
          color: COLORS.muted,
          background: COLORS.surfaceSoft,
          padding: "7px 14px",
          borderRadius: 999,
        }}
      >
        Dibaca
      </span>
    </div>

    <div
      style={{
        marginTop: 32,
        display: "flex",
        flexDirection: "column",
        gap: 14,
        flex: 1,
      }}
    >
      <div
        style={{
          alignSelf: "flex-end",
          background: COLORS.primary,
          color: "#fff",
          borderRadius: "22px 22px 8px 22px",
          padding: "16px 20px",
          fontSize: 22,
          maxWidth: "82%",
        }}
      >
        Halo, lab bisa dipakai Rabu jam 10?
      </div>
      <div
        style={{
          alignSelf: "center",
          fontSize: 17,
          fontWeight: 600,
          color: COLORS.muted,
          background: "rgba(16,24,40,0.05)",
          padding: "7px 16px",
          borderRadius: 999,
        }}
      >
        Sudah dibaca · 09:12
      </div>
      <div
        style={{
          marginTop: "auto",
          alignSelf: "flex-start",
          border: `2px dashed ${COLORS.line}`,
          borderRadius: "22px 22px 22px 8px",
          padding: "18px 22px",
          display: "flex",
          alignItems: "center",
          gap: 12,
          color: COLORS.danger,
          background: COLORS.dangerSoft,
          fontSize: 21,
          fontWeight: 700,
        }}
      >
        <XIcon size={24} color={COLORS.danger} />
        Belum ada balasan
      </div>
    </div>

    <div
      style={{
        marginTop: 24,
        display: "flex",
        alignItems: "center",
        gap: 10,
        color: COLORS.warning,
        background: COLORS.warningSoft,
        borderRadius: 18,
        padding: "14px 18px",
        fontSize: 21,
        fontWeight: 700,
      }}
    >
      <ClockIcon size={24} color={COLORS.warning} />3 jam kemudian…
    </div>
  </CutShell>
);

const ScheduleConflict: React.FC<{ local: number }> = ({ local }) => {
  const stamp = interpolate(local, [8, 18], [0, 1], {
    extrapolateLeft: "clamp",
    extrapolateRight: "clamp",
  });

  return (
    <CutShell local={local}>
      <div
        style={{
          display: "flex",
          alignItems: "center",
          justifyContent: "space-between",
        }}
      >
        <div style={{ fontSize: 26, fontWeight: 800, color: COLORS.ink }}>
          Jadwal Lab — Rabu
        </div>
        <span
          style={{
            fontSize: 17,
            fontWeight: 700,
            color: COLORS.muted,
            background: COLORS.surfaceSoft,
            padding: "7px 14px",
            borderRadius: 999,
          }}
        >
          18 Nov 2026
        </span>
      </div>

      <div
        style={{
          marginTop: 34,
          position: "relative",
          height: 380,
          borderRadius: 24,
          background: COLORS.surfaceSoft,
          border: `1px solid ${COLORS.line}`,
          overflow: "hidden",
        }}
      >
        {["08.00", "10.00", "12.00", "14.00", "16.00"].map((time, i) => (
          <div
            key={time}
            style={{
              position: "absolute",
              top: 18 + i * 84,
              left: 18,
              fontSize: 16,
              fontWeight: 700,
              color: COLORS.muted2,
            }}
          >
            {time}
          </div>
        ))}
        {[0, 1, 2, 3, 4].map((i) => (
          <div
            key={i}
            style={{
              position: "absolute",
              top: 30 + i * 84,
              left: 84,
              right: 22,
              height: 1,
              background: COLORS.line,
            }}
          />
        ))}

        <div
          style={{
            position: "absolute",
            top: 96,
            left: 84,
            right: 90,
            height: 76,
            borderRadius: 14,
            background: "rgba(59,91,253,0.16)",
            border: `2px solid ${COLORS.primary}`,
            padding: "12px 16px",
            color: COLORS.primaryStrong,
            fontSize: 19,
            fontWeight: 800,
          }}
        >
          Praktikum A · 10.00–12.00
        </div>

        <div
          style={{
            position: "absolute",
            top: 150,
            left: 84,
            right: 34,
            height: 76,
            borderRadius: 14,
            background: "rgba(217,45,32,0.14)",
            border: `2px solid ${COLORS.danger}`,
            padding: "12px 16px",
            color: COLORS.danger,
            fontSize: 19,
            fontWeight: 800,
            display: "flex",
            alignItems: "center",
            gap: 10,
          }}
        >
          <AlertIcon size={22} color={COLORS.danger} />
          Kelas B · 11.00–13.00
        </div>

        <div
          style={{
            position: "absolute",
            top: 118,
            right: 26,
            transform: `rotate(-8deg) scale(${0.8 + stamp * 0.2})`,
            opacity: stamp,
            background: COLORS.danger,
            color: "#fff",
            fontSize: 20,
            fontWeight: 800,
            letterSpacing: 1,
            padding: "8px 14px",
            borderRadius: 10,
          }}
        >
          BENTROK
        </div>
      </div>

      <div
        style={{
          marginTop: 26,
          fontSize: 21,
          fontWeight: 700,
          color: COLORS.danger,
          display: "flex",
          alignItems: "center",
          gap: 10,
        }}
      >
        <AlertIcon size={24} color={COLORS.danger} />
        Dua jadwal memakai slot yang sama
      </div>
    </CutShell>
  );
};

const RetryIssue: React.FC<{ local: number }> = ({ local }) => {
  const steps = ["Chat admin", "Tunggu balasan", "Ulangi dari awal"];
  const active = Math.min(2, Math.floor(local / 18));

  return (
    <CutShell local={local}>
      <div style={{ fontSize: 26, fontWeight: 800, color: COLORS.ink }}>
        Prosesnya berulang
      </div>

      <div
        style={{
          flex: 1,
          display: "flex",
          flexDirection: "column",
          justifyContent: "center",
          gap: 22,
        }}
      >
        {steps.map((step, index) => {
          const done = index <= active;
          return (
            <div
              key={step}
              style={{
                display: "flex",
                alignItems: "center",
                gap: 18,
                background: done ? COLORS.surface : COLORS.surfaceSoft,
                border: `2px solid ${done ? COLORS.line : "transparent"}`,
                borderRadius: 22,
                padding: "24px 26px",
                opacity: done ? 1 : 0.35,
                transform: `translateX(${done ? 0 : -18}px)`,
              }}
            >
              <div
                style={{
                  width: 44,
                  height: 44,
                  borderRadius: "50%",
                  background:
                    index === 2 ? COLORS.dangerSoft : COLORS.primarySoft,
                  color: index === 2 ? COLORS.danger : COLORS.primary,
                  display: "flex",
                  alignItems: "center",
                  justifyContent: "center",
                  fontWeight: 800,
                  fontSize: 20,
                }}
              >
                {index === 2 ? "↺" : index + 1}
              </div>
              <div
                style={{
                  fontSize: 23,
                  fontWeight: 700,
                  color: index === 2 ? COLORS.danger : COLORS.ink,
                }}
              >
                {step}
              </div>
            </div>
          );
        })}
      </div>

      <div
        style={{
          display: "flex",
          alignItems: "center",
          gap: 12,
          background: COLORS.dangerSoft,
          color: COLORS.danger,
          borderRadius: 18,
          padding: "16px 20px",
          fontSize: 21,
          fontWeight: 700,
        }}
      >
        <ClockIcon size={24} color={COLORS.danger} />
        Waktu terbuang, jadwal tetap tidak pasti
      </div>
    </CutShell>
  );
};

export const Scene02Problem: React.FC = () => {
  const frame = useCurrentFrame();
  const cut = Math.min(2, Math.floor(frame / 60));
  const local = frame % 60;
  const flash = interpolate(local, [0, 5], [0.18, 0], {
    extrapolateLeft: "clamp",
    extrapolateRight: "clamp",
  });

  return (
    <AbsoluteFill
      style={{
        background:
          "radial-gradient(900px 600px at 100% 0%, #ffe9e6 0%, rgba(244,246,251,0) 55%), radial-gradient(800px 500px at 0% 100%, #eef2ff 0%, rgba(244,246,251,0) 55%), #f4f6fb",
        fontFamily: FONT_FAMILY,
        overflow: "hidden",
      }}
    >
      <div
        style={{
          position: "absolute",
          inset: 0,
          background: COLORS.danger,
          opacity: flash,
          pointerEvents: "none",
        }}
      />

      <SceneHeading
        kicker="Yang bikin capek"
        size={78}
        title={
          <>
            Chat lama.
            <br />
            Jadwal <Highlight>bentrok.</Highlight>
          </>
        }
      />

      <div
        style={{
          position: "absolute",
          top: 640,
          left: 64,
          right: 140,
          height: 760,
          background: "#ffffff",
          borderRadius: 44,
          boxShadow: SHADOW.card,
          overflow: "hidden",
        }}
      >
        {cut === 0 ? <ChatIssue local={local} /> : null}
        {cut === 1 ? <ScheduleConflict local={local} /> : null}
        {cut === 2 ? <RetryIssue local={local} /> : null}
        <div
          style={{
            position: "absolute",
            bottom: 18,
            left: 44,
            display: "flex",
            gap: 8,
          }}
        >
          {[0, 1, 2].map((dot) => (
            <span
              key={dot}
              style={{
                width: dot === cut ? 34 : 10,
                height: 10,
                borderRadius: 999,
                background: dot === cut ? COLORS.danger : COLORS.line,
                transition: "none",
              }}
            />
          ))}
        </div>
      </div>

      <BottomNote>Solusinya harus lebih simpel dari ini.</BottomNote>
    </AbsoluteFill>
  );
};
