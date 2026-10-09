/**
 * Erickson Holding SSO Client Integration
 * Copy this file into your Lovable / client app project.
 *
 * Usage:
 *   import { triggerEricksonSSO } from './erickson-sso-client';
 *   triggerEricksonSSO({ onSuccess: (user) => { console.log(user); } });
 */

const EH_AUTH_URL = "https://accounts.ericksonholding.com"; // Production
// const EH_AUTH_URL = "http://localhost:3001"; // Local dev

const THIS_ORIGIN = window.location.origin; // e.g. https://buyer-finder-shop.lovable.app

/**
 * Opens the Erickson Holding Auth Portal as an SSO popup.
 * On success, calls `onSuccess` with user data.
 * On failure or denial, calls `onError`.
 */
export function triggerEricksonSSO({
  onSuccess,
  onError,
}: {
  onSuccess: (user: EHUser) => void;
  onError?: (reason: string) => void;
}) {
  const encodedOrigin = encodeURIComponent(THIS_ORIGIN);
  const ssoUrl = `${EH_AUTH_URL}/login?isSSO=true&returnOrigin=${encodedOrigin}`;

  const popup = window.open(
    ssoUrl,
    "EricksonHoldingSSO",
    "width=480,height=640,top=100,left=100,scrollbars=yes"
  );

  if (!popup) {
    onError?.("Popup was blocked. Please allow popups for this site.");
    return;
  }

  const handleMessage = (event: MessageEvent) => {
    // SECURITY: Only accept messages from the trusted auth portal
    if (event.origin !== EH_AUTH_URL) return;

    if (event.data?.type === "SSO_AUTH_SUCCESS") {
      window.removeEventListener("message", handleMessage);
      const payload = event.data.payload as EHUser;
      // Store user data so other pages can access it
      sessionStorage.setItem("eh_user", JSON.stringify(payload));
      onSuccess(payload);
    }
  };

  window.addEventListener("message", handleMessage);

  // Clean up if popup is closed manually
  const pollTimer = setInterval(() => {
    if (popup.closed) {
      clearInterval(pollTimer);
      window.removeEventListener("message", handleMessage);
      onError?.("Login popup was closed.");
    }
  }, 500);
}

/**
 * Returns the currently logged-in Erickson Holding user from session storage.
 * Returns null if not logged in.
 */
export function getEHUser(): EHUser | null {
  try {
    const stored = sessionStorage.getItem("eh_user");
    return stored ? JSON.parse(stored) : null;
  } catch {
    return null;
  }
}

/**
 * Clears the Erickson Holding SSO session.
 */
export function clearEHUser(): void {
  sessionStorage.removeItem("eh_user");
}

export interface EHUser {
  uid: string;
  displayName: string | null;
  email: string | null;
  photoURL: string | null;
  idToken: string;
}
