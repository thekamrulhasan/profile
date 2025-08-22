"use client"

import { useEffect, useState } from "react"
import { Card, CardContent } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { MapPin, Calendar, Mail, Phone } from "lucide-react"

export function About() {
  const [isVisible, setIsVisible] = useState(false)

  useEffect(() => {
    const observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) {
          setIsVisible(true)
        }
      },
      { threshold: 0.1 },
    )

    const element = document.getElementById("about")
    if (element) {
      observer.observe(element)
    }

    return () => observer.disconnect()
  }, [])

  const highlights = [
    "CI/CD Pipeline Design & Implementation",
    "Infrastructure as Code (Terraform)",
    "Container Orchestration (Kubernetes)",
    "Cloud Platform Management (AWS, GCP)",
    "Full Stack Web Development",
    "Database Design & Optimization",
    "Monitoring & Performance Optimization",
    "Security Integration & Best Practices",
  ]

  return (
    <section id="about" className="py-20 bg-muted/30">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className={`transition-all duration-1000 ${isVisible ? "animate-fade-in-up" : "opacity-0"}`}>
          {/* Section Header */}
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-foreground mb-4">About Me</h2>
            <p className="text-lg text-muted-foreground max-w-2xl mx-auto">
              Experienced DevOps Engineer with a passion for automation and scalable systems
            </p>
          </div>

          <div className="grid lg:grid-cols-2 gap-12 items-start">
            {/* Left Column - Bio */}
            <div className="space-y-6">
              <Card>
                <CardContent className="p-8">
                  <h3 className="text-2xl font-semibold text-foreground mb-6">Professional Journey</h3>

                  <div className="space-y-4 text-muted-foreground leading-relaxed">
                    <p>
                      With over 5 years of experience in DevOps and full-stack development, I specialize in building
                      robust, scalable infrastructure and automating complex deployment processes.
                    </p>

                    <p>
                      My expertise spans across cloud platforms, containerization, CI/CD pipelines, and modern web
                      development frameworks. I&apos;m passionate about creating efficient workflows that enable teams to
                      deliver high-quality software faster and more reliably.
                    </p>

                    <p>
                      Currently working at Golden Harvest Infotech, where I lead infrastructure automation initiatives
                      and mentor development teams on DevOps best practices.
                    </p>
                  </div>

                  {/* Contact Info */}
                  <div className="mt-8 pt-6 border-t border-border">
                    <div className="grid sm:grid-cols-2 gap-4">
                      <div className="flex items-center gap-3 text-sm text-muted-foreground">
                        <MapPin className="h-4 w-4 text-primary" />
                        <span>Dhaka, Bangladesh</span>
                      </div>
                      <div className="flex items-center gap-3 text-sm text-muted-foreground">
                        <Calendar className="h-4 w-4 text-primary" />
                        <span>Available for Projects</span>
                      </div>
                      <div className="flex items-center gap-3 text-sm text-muted-foreground">
                        <Mail className="h-4 w-4 text-primary" />
                        <span>hasan.kamrul.anik@gmail.com</span>
                      </div>
                      <div className="flex items-center gap-3 text-sm text-muted-foreground">
                        <Phone className="h-4 w-4 text-primary" />
                        <span>+8801729354682</span>
                      </div>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </div>

            {/* Right Column - Highlights */}
            <div className="space-y-6">
              <Card>
                <CardContent className="p-8">
                  <h3 className="text-2xl font-semibold text-foreground mb-6">Core Expertise</h3>

                  <div className="grid gap-3">
                    {highlights.map((highlight, index) => (
                      <div
                        key={index}
                        className={`flex items-center gap-3 p-3 rounded-lg bg-primary/5 border border-primary/10 transition-all duration-500 ${
                          isVisible ? "animate-slide-in-left" : "opacity-0"
                        }`}
                        style={{ animationDelay: `${index * 100}ms` }}
                      >
                        <div className="w-2 h-2 bg-primary rounded-full flex-shrink-0" />
                        <span className="text-foreground font-medium">{highlight}</span>
                      </div>
                    ))}
                  </div>
                </CardContent>
              </Card>

              {/* Certifications */}
              <Card>
                <CardContent className="p-8">
                  <h3 className="text-2xl font-semibold text-foreground mb-6">Certifications & Learning</h3>

                  <div className="flex flex-wrap gap-2">
                    <Badge variant="secondary">AWS Solutions Architect</Badge>
                    <Badge variant="secondary">Kubernetes Administrator</Badge>
                    <Badge variant="secondary">Docker Certified</Badge>
                    <Badge variant="secondary">Terraform Associate</Badge>
                    <Badge variant="secondary">GitLab CI/CD</Badge>
                    <Badge variant="secondary">Linux Professional</Badge>
                  </div>
                </CardContent>
              </Card>
            </div>
          </div>
        </div>
      </div>
    </section>
  )
}
