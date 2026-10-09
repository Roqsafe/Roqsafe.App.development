# SSO Firebase Popup Integration Prompt

**Instructions:** Copy and paste the following prompt into Cursor, Lovable, v0, or your agentic code generator for your separate web app running on `http://localhost:3002`.

---

## **PROMPT START**

I have two applications running locally:

1. **Authentication Portal (Erickson Holding)** running on `http://localhost:3001`
2. **Client App** running on `http://localhost:3002`

Both applications share the exact same Firebase configuration (`com-eh` project).

I want to implement a Single Sign-On (SSO) flow where **App 2 (`localhost:3002`)** opens a popup window to **App 1 (`localhost:3001/login?isSSO=true`)** to log the user in. After the user successfully logs into App 1, the popup window should send the user's data back to the original App 2 window and close itself.

Please implement the following:

### 1. In App 1 (Authentication Portal at `localhost:3001/login`)

Modify my existing login function to check if `window.opener` exists. If it does, once `signInWithEmailAndPassword` or `signInWithPopup(auth, google)` completes successfully, broadcast a message back to App 2 and close the popup:

```javascript
const handleLoginSuccess = (userCredential) => {
  if (window.opener) {
    window.opener.postMessage(
      {
        type: "SSO_AUTH_SUCCESS",
        payload: {
          uid: userCredential.user.uid,
          displayName: userCredential.user.displayName,
          email: userCredential.user.email,
          photoURL: userCredential.user.photoURL,
          // You can also pass the access token using await userCredential.user.getIdToken()
        },
      },
      "http://localhost:3002",
    );
    window.close();
  } else {
    router.push("/");
  }
};
```

### 2. In App 2 (Client App at `localhost:3002`)

Create an SSO Login button component that opens the popup to App 1 and listens for the `message` event.

When the `message` event is received with `type === 'SSO_AUTH_SUCCESS'`, save the user's data to state/localStorage, display their name and photo on the homepage, and handle the authenticated session.

_Note: You can implement this as a React Hook `useSSO()` or directly in the homepage component._

```javascript
const openLoginPopup = () => {
  const popup = window.open(
    "http://localhost:3001/login",
    "Erickson SSO",
    "width=500,height=700",
  );

  const messageListener = (event) => {
    // Ensure the message is strictly from our designated Auth Portal
    if (event.origin !== "http://localhost:3001") return;

    if (event.data.type === "SSO_AUTH_SUCCESS") {
      console.log("Logged in successfully!", event.data.payload);

      // Save the user data to state
      setCurrentUser(event.data.payload);

      // Remove listener
      window.removeEventListener("message", messageListener);
    }
  };

  window.addEventListener("message", messageListener);
};
```

Please generate the React code for **App 2** showing a complete Homepage with a Login button that uses this `window.open` code, and conditionally renders a Welcome screen (with name and photo avatar) once the cross-origin postMessage is received!

---

## **PROMPT END**
