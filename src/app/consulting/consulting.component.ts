import { AfterViewInit, Component } from '@angular/core';
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
    { videoId: 'Uyfk-4m9Syk'}, 
    { videoId: '0Oc1Y_LZXUI'} 
  ];
  playerConfig = {
    controls: 0,
    mute: 1,
    autoplay: 0
  };
}


