"use client";

import type React from "react";
import { QueryClientProvider as TanstackQueryClientProvider } from "@tanstack/react-query";
import { getQueryClient } from "~/lib/getQueryClient";
import { ReactQueryDevtools } from "@tanstack/react-query-devtools";

export default function QueryClientProvider({
  children,
}: {
  children: React.ReactNode;
}) {
  const queryClient = getQueryClient();

  return (
    <TanstackQueryClientProvider client={queryClient}>
      {children}
      <ReactQueryDevtools />
    </TanstackQueryClientProvider>
  );
}
