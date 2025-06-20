import { AfterViewInit, Component, ElementRef, Renderer2 } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { MatIconModule } from '@angular/material/icon';
import { YouTubePlayer, YouTubePlayerModule } from '@angular/youtube-player';
import { CommonModule } from '@angular/common';
import { ScrollButtonComponent } from '../scroll-button/scroll-button.component';

@Component({
  selector: 'app-consulting',
  standalone: true,
  imports: [NavbarComponent,
            FooterComponent,
            MatIconModule,
            RouterOutlet, RouterLink, RouterLinkActive,
            YouTubePlayer, YouTubePlayerModule,
            CommonModule,
            ScrollButtonComponent],
  templateUrl: './consulting.component.html',
  styleUrl: './consulting.component.css'
})
export class ConsultingComponent {
  videos = [ 
    { videoId: 'YX5i8T1ta64'}, 
    // { videoId: 'Uyfk-4m9Syk'}, 
    // { videoId: '0Oc1Y_LZXUI'} 
  ];
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
  activeVideoIndex: number | null = null; 
  scrollStopped: boolean = true; 
  toggleVideo(index: number): void { 
    if (this.activeVideoIndex === index) { 
      this.scrollStopped = !this.scrollStopped;
    } 
    else { 
      this.activeVideoIndex = index; 
      this.scrollStopped = true;
    } 
  }

  onPlayerStateChange(event: any, index: number): void { 
    if ((event.data === 0 || event.data === 2) && this.activeVideoIndex === index) { // Video ended 
      this.scrollStopped = true; // Resume scrolling 
    } else if (event.data === 1 && this.activeVideoIndex === index) { // Video playing 
      this.scrollStopped = true; // Stop scrolling 
      }
  }
}


