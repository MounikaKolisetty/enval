import { AfterViewInit, Component } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { MatIconModule } from '@angular/material/icon';
import { YouTubePlayer, YouTubePlayerModule } from '@angular/youtube-player';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-consulting',
  standalone: true,
  imports: [NavbarComponent,
            FooterComponent,
            MatIconModule,
            RouterOutlet, RouterLink, RouterLinkActive,
            YouTubePlayer, YouTubePlayerModule,
          CommonModule],
  templateUrl: './consulting.component.html',
  styleUrl: './consulting.component.css'
})
export class ConsultingComponent {
  isVideoVisible: boolean = false; 
  playerConfig = {
    controls: 0,
    mute: 1,
    autoplay: 1
  };
  toggleVideo() {
    this.isVideoVisible = !this.isVideoVisible; 
  } 
  hideVideo() {
      this.isVideoVisible = false; 
    } 
  }


