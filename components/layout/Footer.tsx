import { Github, Linkedin, Mail, Phone, MapPin } from "lucide-react"

export function Footer() {
  const currentYear = new Date().getFullYear()

  return (
    <footer className="bg-secondary text-secondary-foreground">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div className="grid md:grid-cols-3 gap-8">
          {/* Contact Info */}
          <div>
            <h3 className="text-lg font-semibold mb-4">Get In Touch</h3>
            <div className="space-y-3">
              <div className="flex items-center gap-3">
                <Mail className="h-4 w-4 text-accent" />
                <span className="text-sm">hasan.kamrul.anik@gmail.com</span>
              </div>
              <div className="flex items-center gap-3">
                <Phone className="h-4 w-4 text-accent" />
                <span className="text-sm">+8801729354682</span>
              </div>
              <div className="flex items-center gap-3">
                <MapPin className="h-4 w-4 text-accent" />
                <span className="text-sm">Dhaka, Bangladesh</span>
              </div>
            </div>
          </div>

          {/* Quick Links */}
          <div>
            <h3 className="text-lg font-semibold mb-4">Quick Links</h3>
            <div className="space-y-2">
              <a href="#about" className="block text-sm hover:text-accent transition-colors">
                About
              </a>
              <a href="#skills" className="block text-sm hover:text-accent transition-colors">
                Skills
              </a>
              <a href="#experience" className="block text-sm hover:text-accent transition-colors">
                Experience
              </a>
              <a href="#projects" className="block text-sm hover:text-accent transition-colors">
                Projects
              </a>
              <a href="#blog" className="block text-sm hover:text-accent transition-colors">
                Blog
              </a>
              <a href="#contact" className="block text-sm hover:text-accent transition-colors">
                Contact
              </a>
            </div>
          </div>

          {/* Social Links */}
          <div>
            <h3 className="text-lg font-semibold mb-4">Connect With Me</h3>
            <div className="flex space-x-4">
              <a
                href="https://github.com/hasankamrul"
                target="_blank"
                rel="noopener noreferrer"
                className="text-secondary-foreground hover:text-accent transition-colors"
              >
                <Github className="h-6 w-6" />
              </a>
              <a
                href="https://linkedin.com/in/hasankamrul"
                target="_blank"
                rel="noopener noreferrer"
                className="text-secondary-foreground hover:text-accent transition-colors"
              >
                <Linkedin className="h-6 w-6" />
              </a>
              <a
                href="mailto:hasan.kamrul.anik@gmail.com"
                className="text-secondary-foreground hover:text-accent transition-colors"
              >
                <Mail className="h-6 w-6" />
              </a>
            </div>
          </div>
        </div>

        <div className="border-t border-secondary-foreground/20 mt-8 pt-8 text-center">
          <p className="text-sm text-secondary-foreground/80">
            © {currentYear} Hasan Kamrul Anik. All rights reserved.
          </p>
        </div>
      </div>
    </footer>
  )
}
