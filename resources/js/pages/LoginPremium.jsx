import React, { useMemo, useState } from "react";
import { motion, AnimatePresence } from "framer-motion";

function useRootData() {
  const el = document.getElementById("login-premium-react-root");
  const actionUrl = el?.dataset?.actionUrl || "/login";
  const csrfToken = el?.dataset?.csrfToken || "";
  const logoUrl = el?.dataset?.logoUrl || "";
  const errorMessage = el?.dataset?.errorMessage || "";
  const oldUsername = el?.dataset?.oldUsername || "";
  const oldPoli = el?.dataset?.oldPoli || "";
  const errorUsername = el?.dataset?.errorUsername || "";
  const errorPassword = el?.dataset?.errorPassword || "";
  const errorPoli = el?.dataset?.errorPoli || "";
  let poli = [];
  try {
    const raw = el?.dataset?.poli || "[]";
    const parsed = JSON.parse(raw);
    poli = Array.isArray(parsed)
      ? parsed.map((p) => ({
          value: p?.kd_poli ?? "",
          label: p?.nm_poli ?? "",
        }))
      : [];
  } catch (_e) {
    poli = [];
  }
  return {
    actionUrl,
    csrfToken,
    logoUrl,
    errorMessage,
    oldUsername,
    oldPoli,
    errorUsername,
    errorPassword,
    errorPoli,
    poli,
  };
}

const easeFast = { duration: 0.16, ease: [0.2, 0.8, 0.2, 1] };
const easeMed = { duration: 0.22, ease: [0.2, 0.8, 0.2, 1] };
const springSoft = { type: "spring", stiffness: 380, damping: 36, mass: 0.6 };

const pageVariants = {
  initial: { opacity: 0 },
  animate: { opacity: 1 },
};
const itemVariants = {
  initial: { opacity: 0, y: 10 },
  animate: { opacity: 1, y: 0, transition: easeFast },
};

export default function LoginPremium() {
  const {
    actionUrl,
    csrfToken,
    logoUrl,
    errorMessage,
    oldUsername,
    oldPoli,
    errorUsername,
    errorPassword,
    errorPoli,
    poli,
  } = useRootData();
  const [submitting, setSubmitting] = useState(false);
  const options = useMemo(() => (Array.isArray(poli) ? poli : []), [poli]);

  return (
    <motion.main
      className="relative min-h-screen flex items-center justify-center p-4"
      style={{
        minHeight: "100vh",
        display: "flex",
        alignItems: "center",
        justifyContent: "center",
        padding: 16,
      }}
      variants={pageVariants}
      initial="initial"
      animate="animate"
      transition={easeMed}
    >
      {/* Elevated Card */}
      <motion.div
        className="relative w-full max-w-[560px]"
        style={{ width: "100%", maxWidth: 560 }}
        initial={{ scale: 0.98, opacity: 0 }}
        animate={{ scale: 1, opacity: 1 }}
        transition={springSoft}
      >
        <motion.div
          className="rounded-2xl bg-white shadow-[0_30px_60px_-15px_rgba(0,0,0,0.25)] ring-1 ring-black/5 overflow-visible"
          initial={{ y: 20, opacity: 0 }}
          animate={{ y: 0, opacity: 1 }}
          transition={easeMed}
        >
          {/* Header */}
          <div
            className="px-10 pt-10 pb-6 text-center"
            style={{
              paddingLeft: 40,
              paddingRight: 40,
              paddingTop: 40,
              paddingBottom: 24,
              textAlign: "center",
            }}
          >
            {logoUrl ? (
              <motion.img
                src={logoUrl}
                alt="Logo"
                className="h-16 w-auto mx-auto"
                style={{
                  height: 64,
                  width: "auto",
                  display: "block",
                  marginLeft: "auto",
                  marginRight: "auto",
                }}
                initial={{ opacity: 0, y: 8 }}
                animate={{ opacity: 1, y: 0 }}
                transition={easeFast}
              />
            ) : null}
            <motion.h1
              className="mt-4 text-slate-900 text-[22px] leading-tight font-semibold"
              variants={itemVariants}
              style={{
                marginTop: 16,
                color: "#0f172a",
                fontSize: 22,
                lineHeight: 1.25,
                fontWeight: 600,
              }}
            >
              Simantri Plus
            </motion.h1>
            <motion.p
              className="mt-1 text-slate-600 text-sm"
              variants={itemVariants}
              style={{ marginTop: 4, color: "#475569", fontSize: 14 }}
            >
              Silakan masuk untuk melanjutkan
            </motion.p>
          </div>

          {/* Error banner */}
          <AnimatePresence initial={false}>
            {errorMessage ? (
              <motion.div
                className="mx-8 mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"
                style={{
                  marginLeft: 32,
                  marginRight: 32,
                  marginBottom: 16,
                  borderRadius: 10,
                  border: "1px solid #fecaca",
                  backgroundColor: "#fef2f2",
                  padding: "8px 12px",
                  fontSize: 14,
                  color: "#b91c1c",
                }}
                initial={{ opacity: 0, y: -6 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: -6 }}
                transition={easeFast}
              >
                {errorMessage}
              </motion.div>
            ) : null}
          </AnimatePresence>

          {/* Form */}
          <form
            action={actionUrl}
            method="post"
            onSubmit={() => setSubmitting(true)}
            className="px-10 pb-10 space-y-5"
            style={{ paddingLeft: 40, paddingRight: 40, paddingBottom: 40 }}
          >
            <input type="hidden" name="_token" value={csrfToken} />

            {/* Username */}
            <motion.div
              variants={itemVariants}
              initial="initial"
              animate="animate"
              className=""
            >
              <label
                htmlFor="username"
                className="block text-sm font-medium text-slate-700"
                style={{
                  display: "block",
                  fontSize: 14,
                  fontWeight: 500,
                  color: "#374151",
                  marginBottom: 8,
                }}
              >
                USERNAME
              </label>
              <div
                className="relative mt-1"
                style={{ position: "relative", marginTop: 4 }}
              >
                <span
                  className="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"
                  style={{
                    position: "absolut",
                    left: 12,
                    top: "75%",
                    transform: "translateY(-50%)",
                    color: "#94a3b8",
                    fontSize: 16,
                  }}
                >
                  <i className="fas fa-user" />
                </span>
                <input
                  id="username"
                  name="username"
                  type="text"
                  placeholder="Masukan Username"
                  className={`w-full rounded-md border ${
                    errorUsername
                      ? "border-red-300 bg-red-50 focus:border-red-400 focus:ring-red-200"
                      : "border-slate-200 bg-white focus:border-indigo-300 focus:ring-indigo-100"
                  } pl-12 pr-3 py-3.5 text-base text-slate-900 outline-none transition-colors focus:ring-2`}
                  style={{
                    width: "100%",
                    borderRadius: 8,
                    border: "1px solid #e2e8f0",
                    paddingLeft: 48,
                    paddingRight: 12,
                    paddingTop: 14,
                    paddingBottom: 14,
                    fontSize: 16,
                    color: "#0f172a",
                  }}
                  autoFocus
                  defaultValue={oldUsername}
                />
              </div>
              {errorUsername ? (
                <motion.div
                  initial={{ opacity: 0, y: -4 }}
                  animate={{ opacity: 1, y: 0 }}
                  exit={{ opacity: 0, y: -4 }}
                  transition={easeFast}
                  className="text-xs text-red-600 mt-1"
                  style={{ fontSize: 12, color: "#dc2626", marginTop: 4 }}
                >
                  {errorUsername}
                </motion.div>
              ) : null}
            </motion.div>

            {/* Password */}
            <motion.div
              variants={itemVariants}
              initial="initial"
              animate="animate"
              className="mt-6"
              style={{ marginTop: 18 }}
            >
              <div
                className="flex items-center justify-between"
                style={{
                  display: "flex",
                  alignItems: "center",
                  justifyContent: "space-between",
                }}
              >
                <label
                  htmlFor="password"
                  className="block text-sm font-medium text-slate-700"
                  style={{
                    display: "block",
                    fontSize: 14,
                    fontWeight: 500,
                    color: "#374151",
                  }}
                >
                  PASSWORD
                </label>
                <a
                  href="#"
                  className="text-xs text-indigo-600 hover:text-indigo-700"
                  style={{ fontSize: 12, color: "#4f46e5" }}
                >
                  Lupa Password?
                </a>
              </div>
              <div
                className="relative mt-1"
                style={{ position: "relative", marginTop: 4 }}
              >
                <span
                  className="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"
                  style={{
                    position: "absolute",
                    left: 12,
                    top: "75%",
                    transform: "translateY(-50%)",
                    color: "#94a3b8",
                    fontSize: 16,
                  }}
                >
                  <i className="fas fa-lock" />
                </span>
                <input
                  id="password"
                  name="password"
                  type="password"
                  placeholder="Masukan Password"
                  className={`w-full rounded-md border ${
                    errorPassword
                      ? "border-red-300 bg-red-50 focus:border-red-400 focus:ring-red-200"
                      : "border-slate-200 bg-white focus:border-indigo-300 focus:ring-indigo-100"
                  } pl-12 pr-3 py-3.5 text-base text-slate-900 outline-none transition-colors focus:ring-2`}
                  style={{
                    width: "100%",
                    borderRadius: 8,
                    border: "1px solid #e2e8f0",
                    paddingLeft: 48,
                    paddingRight: 12,
                    paddingTop: 14,
                    paddingBottom: 14,
                    fontSize: 16,
                    color: "#0f172a",
                  }}
                />
              </div>
              {errorPassword ? (
                <motion.div
                  initial={{ opacity: 0, y: -4 }}
                  animate={{ opacity: 1, y: 0 }}
                  exit={{ opacity: 0, y: -4 }}
                  transition={easeFast}
                  className="text-xs text-red-600 mt-1"
                  style={{ fontSize: 12, color: "#dc2626", marginTop: 4 }}
                >
                  {errorPassword}
                </motion.div>
              ) : null}
            </motion.div>

            {/* Poli */}
            <motion.div
              variants={itemVariants}
              initial="initial"
              animate="animate"
              className="mt-6"
              style={{ marginTop: 18 }}
            >
              <label
                htmlFor="poli"
                className="block text-sm font-medium text-slate-700"
                style={{
                  display: "block",
                  fontSize: 14,
                  fontWeight: 500,
                  color: "#374151",
                }}
              >
                PILIH POLIKLINIK
              </label>
              <div
                className="relative mt-1"
                style={{ position: "relative", marginTop: 4 }}
              >
                <span
                  className="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"
                  style={{
                    position: "absolute",
                    left: 12,
                    top: "75%",
                    transform: "translateY(-50%)",
                    color: "#94a3b8",
                    fontSize: 16,
                  }}
                >
                  <i className="fas fa-hospital" />
                </span>
                <select
                  id="poli"
                  name="poli"
                  className={`w-full rounded-md border ${
                    errorPoli
                      ? "border-red-300 bg-red-50 focus:border-red-400 focus:ring-red-200"
                      : "border-slate-200 bg-white focus:border-indigo-300 focus:ring-indigo-100"
                  } pl-12 pr-10 py-3.5 text-base text-slate-900 outline-none transition-colors focus:ring-2`}
                  style={{
                    width: "100%",
                    borderRadius: 8,
                    border: "1px solid #e2e8f0",
                    paddingLeft: 48,
                    paddingRight: 40,
                    paddingTop: 14,
                    paddingBottom: 14,
                    fontSize: 16,
                    color: "#0f172a",
                  }}
                  defaultValue={oldPoli || (options?.[0]?.value ?? "")}
                >
                  {options.map((opt) => (
                    <option
                      key={opt.value}
                      value={opt.value}
                      className="text-black"
                      style={{ color: "#000" }}
                    >
                      {opt.label}
                    </option>
                  ))}
                </select>
                <span
                  className="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"
                  style={{
                    position: "absolute",
                    right: 12,
                    top: "50%",
                    transform: "translateY(-50%)",
                    color: "#94a3b8",
                  }}
                >
                  <i className="fas fa-chevron-down text-xs" />
                </span>
              </div>
              {errorPoli ? (
                <motion.div
                  initial={{ opacity: 0, y: -4 }}
                  animate={{ opacity: 1, y: 0 }}
                  exit={{ opacity: 0, y: -4 }}
                  transition={easeFast}
                  className="text-xs text-red-600 mt-1"
                  style={{ fontSize: 12, color: "#dc2626", marginTop: 4 }}
                >
                  {errorPoli}
                </motion.div>
              ) : null}
            </motion.div>

            {/* Remember me */}
            <div
              className="flex items-center gap-2"
              style={{
                display: "flex",
                alignItems: "center",
                gap: 8,
                marginTop: 16,
              }}
            >
              <input
                id="remember"
                name="remember"
                type="checkbox"
                className="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                style={{
                  height: 16,
                  width: 16,
                  borderRadius: 4,
                  borderColor: "#d1d5db",
                }}
              />
              <label
                htmlFor="remember"
                className="text-sm text-slate-700"
                style={{ fontSize: 14, color: "#374151" }}
              >
                Ingat Saya
              </label>
            </div>

            {/* Submit */}
            <motion.button
              type="submit"
              className={`w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-md bg-indigo-600 text-white text-base font-medium shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-200 mt-6 ${
                submitting ? "opacity-70 cursor-not-allowed" : ""
              }`}
              style={{
                width: "100%",
                display: "inline-flex",
                alignItems: "center",
                justifyContent: "center",
                gap: 8,
                padding: "12px 16px",
                borderRadius: 8,
                backgroundColor: "#4f46e5",
                color: "#fff",
                fontSize: 16,
                fontWeight: 500,
                marginTop: 24,
              }}
              whileTap={submitting ? {} : { scale: 0.99 }}
              transition={easeFast}
              disabled={submitting}
            >
              <i className="fas fa-sign-in-alt" />
              <span>Masuk</span>
            </motion.button>

            {/* Social buttons (placeholder) */}
            <div
              className="mt-5 flex items-center gap-3 justify-center"
              style={{
                marginTop: 20,
                display: "flex",
                alignItems: "center",
                justifyContent: "center",
                gap: 12,
              }}
            >
              <button
                type="button"
                className="h-9 w-9 rounded-md border border-slate-200 bg-white text-slate-600 hover:bg-slate-50"
                style={{
                  height: 36,
                  width: 36,
                  borderRadius: 8,
                  border: "1px solid #e5e7eb",
                  backgroundColor: "#fff",
                  color: "#475569",
                }}
              >
                <i className="fab fa-facebook-f" />
              </button>
              <button
                type="button"
                className="h-9 w-9 rounded-md border border-slate-200 bg-white text-slate-600 hover:bg-slate-50"
                style={{
                  height: 36,
                  width: 36,
                  borderRadius: 8,
                  border: "1px solid #e5e7eb",
                  backgroundColor: "#fff",
                  color: "#475569",
                }}
              >
                <i className="fab fa-google" />
              </button>
              <button
                type="button"
                className="h-9 w-9 rounded-md border border-slate-200 bg-white text-slate-600 hover:bg-slate-50"
                style={{
                  height: 36,
                  width: 36,
                  borderRadius: 8,
                  border: "1px solid #e5e7eb",
                  backgroundColor: "#fff",
                  color: "#475569",
                }}
              >
                <i className="fab fa-twitter" />
              </button>
            </div>
          </form>
        </motion.div>
      </motion.div>
    </motion.main>
  );
}
