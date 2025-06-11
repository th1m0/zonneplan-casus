import type { Metadata } from "next";
import type React from "react";
import "~/styles/globals.css";

import QueryClientProvider from "./_providers/queryClient";

export const metadata: Metadata = {
  title: "Zonneplan casus",
  description: "Zonneplan casus",
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="nl">
      <body>
        <QueryClientProvider>{children}</QueryClientProvider>
      </body>
    </html>
  );
}
