import { Component, ElementRef, HostListener, OnInit, Renderer2 } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { YouTubePlayer, YouTubePlayerModule } from '@angular/youtube-player';
import { CommonModule } from '@angular/common';
import { ScrollButtonComponent } from '../scroll-button/scroll-button.component';

@Component({
  selector: 'app-events',
  standalone: true,
  imports: [NavbarComponent,
            FooterComponent,
            YouTubePlayer, YouTubePlayerModule,
            ScrollButtonComponent
  ],
  templateUrl: './events.component.html',
  styleUrl: './events.component.css'
})
export class EventsComponent{
  playerConfig = {
    controls: 0,
    mute: 0,
    autoplay: 0
  };
  constructor(private renderer: Renderer2, private el: ElementRef) {}

  ngAfterViewInit(): void {
    this.adjustPlaceholderSize();
    window.addEventListener('resize', this.adjustPlaceholderSize.bind(this)); // Listen to window resize
  }

  adjustPlaceholderSize() {
    const placeholders = this.el.nativeElement.querySelectorAll('youtube-player-placeholder');
    placeholders.forEach((placeholder: HTMLElement) => {
      if (window.innerWidth <= 768) {
        // Mobile / tablet view
        this.renderer.setStyle(placeholder, 'width', '100%');
        this.renderer.setStyle(placeholder, 'height', '100%');
      } else {
        // Desktop view
        this.renderer.setStyle(placeholder, 'width', '640px'); // Set your desired desktop width
        this.renderer.setStyle(placeholder, 'height', '390px'); // Set your desired desktop height
      }
    });
  }
}
