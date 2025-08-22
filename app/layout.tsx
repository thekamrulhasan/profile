import type React from "react"
import type { Metadata } from "next"
import { GeistSans } from "geist/font/sans"
import { Manrope } from "next/font/google"
import "./globals.css"

const manrope = Manrope({
  subsets: ["latin"],
  display: "swap",
  variable: "--font-manrope",
})

export const metadata: Metadata = {
  title: "Hasan Kamrul Anik - DevOps Engineer & Full Stack Developer",
  description:
    "Professional DevOps Engineer and Full Stack Developer specializing in CI/CD, Infrastructure as Code, and modern web development.",
  keywords: "DevOps, Full Stack Developer, Laravel, Python, AWS, Docker, Kubernetes, CI/CD",
  authors: [{ name: "Hasan Kamrul Anik" }],
  openGraph: {
    title: "Hasan Kamrul Anik - DevOps Engineer",
    description: "Professional DevOps Engineer and Full Stack Developer",
    type: "website",
    locale: "en_US",
  },
  twitter: {
    card: "summary_large_image",
    title: "Hasan Kamrul Anik - DevOps Engineer",
    description: "Professional DevOps Engineer and Full Stack Developer",
  },
    generator: 'v0.app'
}

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html lang="en" className={`${GeistSans.variable} ${manrope.variable} antialiased`}>
      <body className="font-sans">{children}</body>
    </html>
  )
}