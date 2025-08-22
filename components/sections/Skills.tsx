"use client"

import { useEffect, useState, useMemo } from "react"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"

interface Skill {
  id: number
  name: string
  category: string
  proficiency_level: number
  icon?: string
  description?: string
  is_featured: boolean
  is_active: boolean
}

export function Skills() {
  const [skills, setSkills] = useState<Skill[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [isVisible, setIsVisible] = useState(false)
  const [animatedSkills, setAnimatedSkills] = useState<Set<string>>(new Set())

  useEffect(() => {
    const fetchSkills = async () => {
      setLoading(true)
      setError(null)
      try {
        const response = await fetch("http://127.0.0.1:8000/api/v1/skills")
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`)
        }
        const data = await response.json()
        setSkills(data.data)
      } catch (e: any) {
        setError(e.message)
      } finally {
        setLoading(false)
      }
    }

    fetchSkills()
  }, [])

  const categories = useMemo(() => Array.from(new Set(skills.map((skill) => skill.category))), [skills])

  useEffect(() => {
    const observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) {
          setIsVisible(true)
          // Animate skills progressively
          skills.forEach((skill, index) => {
            setTimeout(() => {
              setAnimatedSkills((prev) => new Set([...prev, skill.name]))
            }, index * 100)
          })
        }
      },
      { threshold: 0.1 },
    )

    const element = document.getElementById("skills")
    if (element) {
      observer.observe(element)
    }

    return () => observer.disconnect()
  }, [skills])

  const getSkillColor = (level: number) => {
    if (level >= 80) return "bg-chart-1"
    if (level >= 60) return "bg-chart-2"
    return "bg-chart-3"
  }

  if (loading) {
    return (
      <section id="skills" className="py-20 bg-background">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center">
            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto"></div>
            <p className="mt-4 text-muted-foreground">Loading skills...</p>
          </div>
        </div>
      </section>
    )
  }

  if (error) {
    return (
      <section id="skills" className="py-20 bg-background">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center text-red-500">
            <p>Error loading skills: {error}</p>
          </div>
        </div>
      </section>
    )
  }

  return (
    <section id="skills" className="py-20 bg-background">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className={`transition-all duration-1000 ${isVisible ? "animate-fade-in-up" : "opacity-0"}`}>
          {/* Section Header */}
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-foreground mb-4">Technical Skills</h2>
            <p className="text-lg text-muted-foreground max-w-2xl mx-auto">
              A comprehensive overview of my technical expertise and proficiency levels
            </p>
          </div>

          {/* Skills by Category */}
          <div className="space-y-12">
            {categories.map((category, categoryIndex) => (
              <div
                key={category}
                className={`transition-all duration-1000 ${isVisible ? "animate-fade-in-up" : "opacity-0"}`}
                style={{ animationDelay: `${categoryIndex * 200}ms` }}
              >
                <Card>
                  <CardHeader>
                    <CardTitle className="text-xl font-semibold text-foreground flex items-center gap-3">
                      <div className="w-1 h-6 bg-primary rounded-full" />
                      {category}
                    </CardTitle>
                  </CardHeader>
                  <CardContent>
                    <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                      {skills
                        .filter((skill) => skill.category === category)
                        .map((skill, index) => (
                          <div key={skill.name} className="space-y-3">
                            <div className="flex items-center justify-between">
                              <span className="font-medium text-foreground">{skill.name}</span>
                              <Badge variant="outline" className="text-xs">
                                {skill.proficiency_level}%
                              </Badge>
                            </div>

                            <div className="relative">
                              <div className="w-full bg-muted rounded-full h-2">
                                <div
                                  className={`h-2 rounded-full transition-all duration-1500 ease-out ${getSkillColor(skill.proficiency_level)}`}
                                  style={{
                                    width: animatedSkills.has(skill.name) ? `${skill.proficiency_level}%` : "0%",
                                  }}
                                />
                              </div>
                            </div>
                          </div>
                        ))}
                    </div>
                  </CardContent>
                </Card>
              </div>
            ))}
          </div>

          {/* Skill Summary */}
          <div className="mt-16 text-center">
            <Card className="max-w-4xl mx-auto">
              <CardContent className="p-8">
                <h3 className="text-2xl font-semibold text-foreground mb-6">Skill Summary</h3>
                <div className="grid md:grid-cols-3 gap-8">
                  <div className="text-center">
                    <div className="text-3xl font-bold text-primary mb-2">
                      {skills.filter((s) => s.proficiency_level >= 80).length}
                    </div>
                    <div className="text-sm text-muted-foreground">Expert Level</div>
                  </div>
                  <div className="text-center">
                    <div className="text-3xl font-bold text-chart-2 mb-2">
                      {skills.filter((s) => s.proficiency_level >= 60 && s.proficiency_level < 80).length}
                    </div>
                    <div className="text-sm text-muted-foreground">Advanced Level</div>
                  </div>
                  <div className="text-center">
                    <div className="text-3xl font-bold text-chart-3 mb-2">
                      {skills.filter((s) => s.proficiency_level < 60).length}
                    </div>
                    <div className="text-sm text-muted-foreground">Intermediate Level</div>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </section>
  )
}