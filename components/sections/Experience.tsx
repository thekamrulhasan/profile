"use client"

import { useState, useEffect } from "react"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Calendar, MapPin, Building, ChevronDown, ChevronUp } from "lucide-react"

interface ExperienceItem {
  id: string
  company_name: string
  position: string
  location: string
  start_date: string
  end_date: string | null
  is_current: boolean
  description: string
  technologies: string[]
  achievements?: string[]
  formatted_date_range: string
}

export function Experience() {
  const [experiences, setExperiences] = useState<ExperienceItem[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [expandedItems, setExpandedItems] = useState<Set<string>>(new Set())

  useEffect(() => {
    const fetchExperiences = async () => {
      setLoading(true)
      setError(null)
      try {
        const response = await fetch("http://127.0.0.1:8000/api/v1/experience")
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`)
        }
        const data = await response.json()
        setExperiences(data.data)
      } catch (e: any) {
        setError(e.message)
      } finally {
        setLoading(false)
      }
    }

    fetchExperiences()
  }, [])

  const toggleExpanded = (id: string) => {
    const newExpanded = new Set(expandedItems)
    if (newExpanded.has(id)) {
      newExpanded.delete(id)
    } else {
      newExpanded.add(id)
    }
    setExpandedItems(newExpanded)
  }

  if (loading) {
    return (
      <section id="experience" className="py-20 bg-gradient-to-br from-slate-50 to-emerald-50">
        <div className="container mx-auto px-4 max-w-4xl">
          <div className="text-center">
            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto"></div>
            <p className="mt-4 text-muted-foreground">Loading experiences...</p>
          </div>
        </div>
      </section>
    )
  }

  if (error) {
    return (
      <section id="experience" className="py-20 bg-gradient-to-br from-slate-50 to-emerald-50">
        <div className="container mx-auto px-4 max-w-4xl">
          <div className="text-center text-red-500">
            <p>Error loading experiences: {error}</p>
          </div>
        </div>
      </section>
    )
  }

  return (
    <section id="experience" className="py-20 bg-gradient-to-br from-slate-50 to-emerald-50">
      <div className="container mx-auto px-4 max-w-4xl">
        <div className="text-center mb-16">
          <h2 className="text-4xl font-bold text-slate-900 mb-4">Professional Experience</h2>
          <p className="text-lg text-slate-600 max-w-2xl mx-auto">
            Over 6 years of experience in DevOps, full-stack development, and infrastructure automation
          </p>
        </div>

        <div className="space-y-8">
          {experiences.map((experience, index) => (
            <Card
              key={experience.id}
              className="group hover:shadow-xl transition-all duration-300 border-l-4 border-l-emerald-500"
            >
              <CardHeader className="pb-4">
                <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                  <div className="flex-1">
                    <CardTitle className="text-xl font-bold text-slate-900 mb-2">{experience.position}</CardTitle>
                    <div className="flex flex-wrap items-center gap-4 text-slate-600">
                      <div className="flex items-center gap-2">
                        <Building className="h-4 w-4" />
                        <span className="font-medium">{experience.company_name}</span>
                      </div>
                      <div className="flex items-center gap-2">
                        <MapPin className="h-4 w-4" />
                        <span>{experience.location}</span>
                      </div>
                      <div className="flex items-center gap-2">
                        <Calendar className="h-4 w-4" />
                        <span>
                          {experience.formatted_date_range}
                        </span>
                      </div>
                    </div>
                  </div>
                  <Button
                    variant="ghost"
                    size="sm"
                    onClick={() => toggleExpanded(experience.id)}
                    className="self-start md:self-center"
                  >
                    {expandedItems.has(experience.id) ? (
                      <>
                        <ChevronUp className="h-4 w-4 mr-2" />
                        Less Details
                      </>
                    ) : (
                      <>
                        <ChevronDown className="h-4 w-4 mr-2" />
                        More Details
                      </>
                    )}
                  </Button>
                </div>
              </CardHeader>

              <CardContent className="pt-0">
                <p className="text-slate-700 mb-6 leading-relaxed">{experience.description}</p>

                {expandedItems.has(experience.id) && (
                  <div className="space-y-6 animate-in slide-in-from-top-2 duration-300">
                    <div>
                      <h4 className="font-semibold text-slate-900 mb-3">Key Responsibilities</h4>
                      <ul className="space-y-2">
                        {experience.responsibilities && experience.responsibilities.map((responsibility, idx) => (
                          <li key={idx} className="flex items-start gap-3">
                            <div className="w-2 h-2 bg-emerald-500 rounded-full mt-2 flex-shrink-0" />
                            <span className="text-slate-700">{responsibility}</span>
                          </li>
                        ))}
                      </ul>
                    </div>

                    {experience.achievements && (
                      <div>
                        <h4 className="font-semibold text-slate-900 mb-3">Key Achievements</h4>
                        <ul className="space-y-2">
                          {experience.achievements.map((achievement, idx) => (
                            <li key={idx} className="flex items-start gap-3">
                              <div className="w-2 h-2 bg-amber-500 rounded-full mt-2 flex-shrink-0" />
                              <span className="text-slate-700 font-medium">{achievement}</span>
                            </li>
                          ))}
                        </ul>
                      </div>
                    )}

                    <div>
                      <h4 className="font-semibold text-slate-900 mb-3">Technologies Used</h4>
                      <div className="flex flex-wrap gap-2">
                        {experience.technologies.map((tech, idx) => (
                          <Badge
                            key={idx}
                            variant="secondary"
                            className="bg-emerald-100 text-emerald-800 hover:bg-emerald-200"
                          >
                            {tech}
                          </Badge>
                        ))}
                      </div>
                    </div>
                  </div>
                )}
              </CardContent>
            </Card>
          ))}
        </div>

        <div className="text-center mt-12">
          <p className="text-slate-600 mb-4">Want to know more about my professional journey?</p>
          <Button size="lg" className="bg-emerald-600 hover:bg-emerald-700 text-white">
            Download Full Resume
          </Button>
        </div>
      </div>
    </section>
  )
}